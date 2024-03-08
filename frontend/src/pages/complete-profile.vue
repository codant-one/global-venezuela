<script setup>

import { avatarText } from '@/@core/utils/formatters'
import { emailValidator, requiredValidator, phoneValidator } from '@/@core/utils/validators'
import { initialAbility } from '@/plugins/casl/ability'
import { useAppAbility } from '@/plugins/casl/useAppAbility'
import { useAuthStores } from '@/stores/useAuth'
import { useProfileStores } from '@/stores/useProfile'
import { useStatesStores } from '@/stores/useStates'
import { useCitiesStores } from '@/stores/useCities'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'
import { useGendersStores } from '@/stores/useGenders'

const router = useRouter()
const ability = useAppAbility()
const statesStores = useStatesStores()
const citiesStores = useCitiesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()
const gendersStores = useGendersStores()
const authStores = useAuthStores()
const profileStores = useProfileStores()

const listStates = ref([])
const listCities = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])
const listGenders = ref([])

const listCitiesByStates = ref([])
const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])

const refVForm = ref()
const user_id = ref('')
const email = ref('')
const name = ref('')
const last_name = ref('')
const phone = ref('')
const address = ref('')
const avatar = ref('')
const gender_id = ref('')
const genderOld_id = ref('')
const document_ = ref('')
const state_id = ref('')
const stateOld_id = ref('')
const city_id = ref('')
const cityOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')

const avatarOld = ref('')

const isRequestOngoing = ref(true)

const alert = ref({
  message: '',
  show: false,  
  type: '',
})

const getCities = computed(() => {
  return listCitiesByStates.value.map((state) => {
    return {
      title: state.name,
      value: state.id,
    }
  })
})

const getMunicipalities = computed(() => {
  return listMunicipalitiesByStates.value.map((state) => {
    return {
      title: state.name,
      value: state.id,
    }
  })
})

const getParishes = computed(() => {
  return listParishesByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

onMounted(async () => {

  isRequestOngoing.value = true

  await statesStores.fetchStates();
  await citiesStores.fetchCities();
  await municipalitiesStores.fetchMunicipalities();
  await parishesStores.fetchParishes();
  await gendersStores.fetchGenders();
  
  loadStates()
  loadCities()
  loadMunicipalities()
  loadParishes()
  loadGenders()

  isRequestOngoing.value = false
})

watchEffect(fetchData)

async function fetchData() {

    let data = JSON.parse(localStorage.getItem('user_data') || 'null')
    
    user_id.value = data.id
    email.value = data.email
    name.value = data.name
    last_name.value = data.last_name
    phone.value = data.user_details?.phone
    address.value = data.user_details?.address
    document_.value = data.user_details?.document

    avatarOld.value = data.user_details?.avatar

    genderOld_id.value = data.user_details?.gender.id
    gender_id.value = data.user_details?.gender.name

    stateOld_id.value = data.user_details?.parish.municipality.state.id
    state_id.value = data.user_details?.parish.municipality.state.name

    cityOld_id.value = data.user_details?.city.id
    city_id.value = data.user_details?.city.name

    municipalityOld_id.value = data.user_details?.parish.municipality.id
    municipality_id.value = data.user_details?.parish.municipality.name

    parishOld_id.value = data.user_details?.parish.id
    parish_id.value = data.user_details?.parish.name
}

const loadStates = () => {
  listStates.value = statesStores.getStates
}

const loadCities = () => {
  listCities.value = citiesStores.getCities
}

const loadMunicipalities = () => {
  listMunicipalities.value = municipalitiesStores.getMunicipalities
}

const loadParishes = () => {
  listParishes.value = parishesStores.getParishes
}

const loadGenders = () => {
  listGenders.value = gendersStores.getGenders
}

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state)
    state_id.value = _state.name
 
    city_id.value = ''
    municipality_id.value = ''

    listCitiesByStates.value = listCities.value.filter(item => item.state_id === _state.id)
    listMunicipalitiesByStates.value = listMunicipalities.value.filter(item => item.state_id === _state.id)
  }
}

const selectMunicipalities = municipality => {
  if (municipality) {
    let _municipality = listMunicipalities.value.find(item => item.id === municipality)
    municipality_id.value = _municipality.name
 
    parish_id.value = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const logout = () => {

  authStores.logout()
    .then(response => {
    // Remove "user_data" from localStorage
    localStorage.removeItem('user_data')

    // Remove "accessToken" from localStorage
    localStorage.removeItem('accessToken')
    
    // Remove "userAbilities" from localStorage
    localStorage.removeItem('userAbilities')

    // Reset ability to initial ability
    ability.update(initialAbility)
    router.push('/login')

  })
}

const resetAvatar = () => {
  avatar.value = null
}

const onSubmit = () =>{
  refVForm.value?.validate().then(({ valid: isValid }) => {

    if (isValid) {

      let formData = new FormData()
      
      formData.append('user_id', user_id.value)
      formData.append('email', email.value)
      formData.append('name', name.value)
      formData.append('last_name', last_name.value)
      formData.append('phone', phone.value)
      formData.append('address', address.value)
      formData.append('image', avatarOld.value)
      formData.append('document', document_.value)
      formData.append('gender_id', (Number.isInteger(gender_id.value)) ? gender_id.value : genderOld_id.value)
      formData.append('parish_id', (Number.isInteger(parish_id.value)) ? parish_id.value : parishOld_id.value)
      formData.append('city_id', (Number.isInteger(city_id.value)) ? city_id.value : cityOld_id.value)

      profileStores.updateData(formData)
        .then(response => {    

          window.scrollTo(0, 0)
          
          alert.value.type = 'success'
          alert.value.message = 'Información personal actualizada. se recargara la página automaticamente para observar los efectos..!'
          alert.value.show = true
          
          localStorage.setItem('user_data', JSON.stringify(response.user_data))
          
          setTimeout(() => {
            alert.value.show=false,
            alert.value.message = ''
            location.reload()
          }, 5000)

        }).catch(error => {
          alert.value.type = 'error'
          alert.value.show = true
          alert.value.message = 'Se ha producido un error...! (Server Error)'
          
          setTimeout(() => {
            alert.value.show = false,
            alert.value.message = ''
          }, 5000) 
        })
    }
  })
}

const onImageSelected = event => {
  const file = event.target.files[0]

  if (!file) return
  // avatarOld.value = file

  URL.createObjectURL(file)

  resizeImage(file, 400, 400, 0.9)
    .then(async blob => {
      avatarOld.value = blob
      let r = await blobToBase64(blob)
      avatar.value = 'data:image/jpeg;base64,' + r
    })
}

const resizeImage = function(file, maxWidth, maxHeight, quality) {
  return new Promise((resolve, reject) => {
    const img = new Image()

    img.src = URL.createObjectURL(file)
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')

      let width = img.width
      let height = img.height

      if (maxWidth && width > maxWidth) {
        height *= maxWidth / width
        width = maxWidth
      }

      if (maxHeight && height > maxHeight) {
        width *= maxHeight / height
        height = maxHeight
      }

      canvas.width = width
      canvas.height = height

      ctx.drawImage(img, 0, 0, width, height)

      canvas.toBlob(blob => {
        resolve(blob)
      }, file.type, quality)
    }
    img.onerror = error => {
      reject(error)
    }
  })
}

const blobToBase64 = blob => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.readAsDataURL(blob)
    reader.onload = () => {
      resolve(reader.result.split(',')[1])
    }
    reader.onerror = error => {
      reject(error)
    }
  })
}
</script>

<template>
  <VRow class="justify-center m-0">

    <VDialog
      v-model="isRequestOngoing"
      width="300"
      persistent>
          
      <VCard
        color="primary"
        width="300">
            
        <VCardText class="pt-3">
          Cargando

          <VProgressLinear
            indeterminate
            color="white"
            class="mb-0"/>
        </VCardText>
      </VCard>
    </VDialog>

    <VCol
      cols="12"
      md="8"
    >
      <h1 class="text-center">
        Completar Perfil del Usuario
      </h1>
      <VCard
        title="¡Atención!"
        class="mb-5"
      >
        <VCardText>
          <p>Para poder utilizar nuestro panel con 
            normalidad, necesitamos que la primera vez que ingreses a él, nos rellenes el formulario con tus datos. Para que luego puedas utilizar todas las funciones que tenemos preparadas para ti.</p>
          <p>¡Bienvenid@ a VENEZUELA GLOBAL! Cualquier duda, puedes ponerte en contacto con nosotros 📩.</p>
        </VCardText>
      </VCard>
      <VDivider />
      <VRow>
        <VCol 
          v-if="alert.show" 
          cols="12" 
        >
          <VAlert
            v-if="alert.show"
            :type="alert.type"
          >
            {{ alert.message }}
          </VAlert>
        </VCol>
        <VCol cols="12">
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VCardText class="d-block d-md-flex">
              <VAvatar
                rounded
                size="100"
                class="me-6"
                :color="avatar ? 'default' : 'primary'"
                variant="tonal"
              >
                <VImg
                  v-if="avatar"
                  style="border-radius: 6px;"
                  :src="avatar"
                />
                <span
                  v-else
                  class="text-5xl font-weight-semibold"
                >
                  {{ avatarText(name) }}
                </span>
              </VAvatar>

              <!-- 👉 Upload Photo -->
              <div class="d-flex flex-column justify-center gap-4 mt-4">
                <div class="d-flex flex-wrap gap-2">
                  <VFileInput                          
                    label="Avatar"
                    accept="image/png, image/jpeg, image/bmp"
                    placeholder="Avatar"
                    prepend-icon="tabler-camera"
                    :rules="[requiredValidator]"
                    @change="onImageSelected"
                    @click:clear="resetAvatar"
                  />
                </div>
                <p class="text-body-1 mb-0">
                  Formatos Permitidos JPG, GIF, PNG.
                </p>
              </div>
            </VCardText>

            <VDivider />

            <VCardText class="pt-2 mt-6">
              <VRow>
                <VCol
                  md="6"
                  cols="12"
                >
                  <VTextField
                    v-model="name"
                    label="Nombre"
                    :rules="[requiredValidator]"
                    readonly
                  />
                </VCol>
                <VCol
                  md="6"
                  cols="12"
                >
                  <VTextField
                    v-model="last_name"
                    label="Apellidos"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="email"
                    label="E-mail"
                    type="email"
                    :rules="[requiredValidator, emailValidator]"
                    readonly
                  />
                </VCol>

                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="phone"
                    label="Teléfono"
                    placeholder="+(XX) XXXXXXXXX"
                    :rules="[phoneValidator, requiredValidator]"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VAutocomplete
                    v-model="gender_id"
                    label="Género"
                    :rules="[requiredValidator]"
                    :items="listGenders"
                    item-title="name"
                    item-value="id"
                    :menu-props="{ maxHeight: '200px' }"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VTextField
                    v-model="document_"
                    label="Cédula"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VAutocomplete
                    v-model="state_id"
                    label="Estado"
                    :rules="[requiredValidator]"
                    :items="listStates"
                    item-title="name"
                    item-value="name"
                    :menu-props="{ maxHeight: '200px' }"
                    @update:model-value="selectState"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VAutocomplete
                    v-model="city_id"
                    label="Ciudad"
                    :rules="[requiredValidator]"
                    :items="getCities"
                    :menu-props="{ maxHeight: '200px' }"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VAutocomplete
                    v-model="municipality_id"
                    label="Municipio"
                    :rules="[requiredValidator]"
                    :items="getMunicipalities"
                    :menu-props="{ maxHeight: '200px' }"
                    @update:model-value="selectMunicipalities"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VAutocomplete
                    v-model="parish_id"
                    label="Parroquia"
                    :rules="[requiredValidator]"
                    :items="getParishes"
                    :menu-props="{ maxHeight: '200px' }"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="12"
                >
                  <VTextarea
                    v-model="address"
                    rows="3"
                    label="Dirección"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <!-- 👉 Form Actions -->
                <VCol
                  cols="12" md="12"
                  class="d-flex flex-wrap gap-4 justify-buttons"
                >
                  <VBtn type="submit">
                    Guardar Cambios
                  </VBtn>
                  <VBtn @click="logout">
                    Cerrar Sesión
                  </VBtn>
                </VCol>
              </VRow>
            </VCardText>
          </VForm>
        </VCol>
      </VRow>
    </VCol>
  </VRow>
</template>

<style lang="scss">
  .m-0 {
    margin: 0;
  }

  .justify-buttons {
    justify-content: right !important;

    @media (max-width: 767px) {
      justify-content: center !important;
    }
  }
</style>

<route lang="yaml">
  meta:
    layout: blank
    action: ver
    subject: Auth
    parar: true
  </route>
