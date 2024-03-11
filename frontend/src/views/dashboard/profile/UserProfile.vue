<script setup>

import { avatarText } from '@/@core/utils/formatters'
import { emailValidator, requiredValidator, phoneValidator } from '@/@core/utils/validators'
import { useProfileStores } from '@/stores/useProfile'

const props = defineProps({
  states: {
    type: Object,
    required: true
  },
  cities: {
    type: Object,
    required: true
  },
  municipalities: {
    type: Object,
    required: true
  },
  parishes: {
    type: Object,
    required: true
  },
  genders: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  avatarOld: {
    type: [String, Object],
    required: true
  },
  avatar: {
    type: String,
    required: true
  }
})

const emit = defineEmits([
  'onImageSelected',
])

const profileStores = useProfileStores()

const listStates = ref(props.states)
const listCities = ref(props.cities)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listGenders = ref(props.genders)

const listCitiesByStates = ref([])
const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])


const refVForm = ref()
const isUserEditDialog = ref(false)

const userData = ref(props.user)
const user_id = ref('')
const email = ref('')
const name = ref('')
const last_name = ref('')
const phone = ref('')
const address = ref('')
const document = ref('')
const avatar = ref(props.avatar)
const gender_id = ref('')
const genderOld_id = ref('')
const gender_ = ref('')
const state_id = ref('')
const stateOld_id = ref('')
const state_ = ref('')
const city_id = ref('')
const cityOld_id = ref('')
const city_ = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const municipality_ = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const parish_ = ref('')

const avatarOld = ref(props.avatarOld)
const roles = ref('')

const alert = ref({
    message: '',
    show: false,  
    type: ''
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

watch(() =>  
  props.avatar, (avatar_) => {
    avatar.value = avatar_
  });

watch(() => 
  props.avatarOld, (avatarOld_) => {
    avatarOld.value = avatarOld_
  });

watchEffect(fetchData)

async function fetchData() { 

    user_id.value = userData.value.id
    email.value = userData.value.email
    name.value = userData.value.name
    last_name.value = userData.value.last_name ?? ''
    phone.value = userData.value.user_details?.phone
    address.value = userData.value.user_details?.address
    document.value = userData.value.user_details?.document
   
    genderOld_id.value = userData.value.user_details?.gender.id
    gender_id.value = userData.value.user_details?.gender.name
    gender_.value = userData.value.user_details?.gender.name

    stateOld_id.value = userData.value.user_details?.parish.municipality.state.id
    state_id.value = userData.value.user_details?.parish.municipality.state.name
    state_.value = userData.value.user_details?.parish.municipality.state.name

    cityOld_id.value = userData.value.user_details?.city.id
    city_id.value = userData.value.user_details?.city.name
    city_.value = userData.value.user_details?.city.name

    municipalityOld_id.value = userData.value.user_details?.parish.municipality.id
    municipality_id.value = userData.value.user_details?.parish.municipality.name
    municipality_.value = userData.value.user_details?.parish.municipality.name

    parishOld_id.value = userData.value.user_details?.parish.id
    parish_id.value = userData.value.user_details?.parish.name
    parish_.value = userData.value.user_details?.parish.name

    roles.value = userData.value.roles
   
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
            formData.append('document', document.value)
            formData.append('gender_id', (Number.isInteger(gender_id.value)) ? gender_id.value : genderOld_id.value)
            formData.append('parish_id', (Number.isInteger(parish_id.value)) ? parish_id.value : parishOld_id.value)
            formData.append('city_id', (Number.isInteger(city_id.value)) ? city_id.value : cityOld_id.value)

            formData.append('image', avatarOld.value)

            profileStores.updateData(formData)
                .then(response => {    

                    window.scrollTo(0, 0)
                    
                    alert.value.type = 'success'
                    alert.value.message = 'Información personal actualizada. se recargara la página automaticamente para observar los efectos..!'
                    alert.value.show = true
                    
                    localStorage.setItem('user_data', JSON.stringify(response.user_data))
                    
                    fetchData()

                    setTimeout(() => {
                        alert.value.show = false,
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

const deleteAvatar = ()=>{
    avatarOld.value = null
    resetAvatar()
}

const showUserEditDialog = u =>{
  isUserEditDialog.value = true
}

const closeUserEditDialog = ()=>{
  isUserEditDialog.value = false
  fetchData()
}
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard>
          <VCardText class="text-center pt-6">
            <VAvatar
              rounded
              :size="120"
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
            <h6 class="text-h6 mt-4">
              {{ name.toUpperCase() }} {{ last_name.toUpperCase() }}
            </h6>

            <!-- 👉 Role chip -->
            <VChip
              v-for="rol in roles"
              :key="rol"
              label
              size="small"
              class="text-capitalize mt-4 mr-1"
            >
              {{ rol.name }}
            </VChip>
          </VCardText>

          <VDivider />

          <!-- 👉 Details -->
          <VCardText>
            <p class="text-sm text-uppercase text-disabled">
              Detalles
            </p>

            <VList class="card-list mt-2">
              <VListItem>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Nombre:
                    <span class="text-body-2">
                      {{ name }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Apellido:
                    <span class="text-body-2">
                      {{ last_name }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Email:
                    <span class="text-body-2">
                      {{ email }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                      Teléfono:
                    <span class="text-body-2">
                      {{ phone }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Género:
                    <span class="text-body-2">
                      {{ gender_ }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Cédula:
                    <span class="text-body-2">
                      {{ document }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Estado:
                    <span class="text-body-2">
                     {{ state_ }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Ciudad:
                    <span class="text-body-2">
                     {{ city_ }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Municipio:
                    <span class="text-body-2">
                     {{ municipality_ }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Parroquia:
                    <span class="text-body-2">
                     {{ parish_ }}
                    </span>
                  </h6>
                </VListItemTitle>
                <VListItemTitle>
                  <h6 class="text-base font-weight-semibold">
                    Dirección:
                    <span class="text-body-2">
                      {{ address }}
                    </span>
                  </h6>
                </VListItemTitle>
              </VListItem>
            </VList>
          </VCardText>

          <!-- 👉 Edit and Suspend button -->
          <VCardText class="d-flex justify-center">
            <VBtn
              variant="elevated"
              class="me-3"
              @click="showUserEditDialog()"
            >
              Editar
            </VBtn>
          </VCardText>
        </VCard>
      </VCol>

      <!-- DIALOGO DE EDITAR Información PERSONAL -->
      <VDialog
        v-model="isUserEditDialog"
        max-width="800"
        persistent
      >
        <!-- Dialog close btn -->
        <DialogCloseBtn @click="closeUserEditDialog" />

        <!-- Dialog Content -->
        <VCard title="Editar Información Personal">    
          <VDivider class="mt-4"/>    
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
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VCardText class="d-flex">
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
              <div class="d-flex flex-column justify-center gap-4">
                <div class="d-flex flex-wrap gap-2">
                  <VFileInput                          
                    label="Avatar"
                    accept="image/png, image/jpeg, image/bmp"
                    placeholder="Avatar"
                    prepend-icon="tabler-camera"
                    @change="$emit('onImageSelected', $event)"
                    @click:clear="resetAvatar"
                  />
                </div>
                <p class="text-body-1 mb-0">
                  Formatos Permitidos JPG, GIF, PNG.
                </p>
                <VBtn 
                  color="secondary"
                  variant="tonal"
                  @click="deleteAvatar"
                >
                  Eliminar Avatar
                </VBtn>
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
                    label="Nombres"
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
                    label="Telefono"
                    placeholder="+(XX) XXXXXXXXX"
                    :rules="[requiredValidator, phoneValidator]"
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
                    v-model="document"
                    type="tel"
                    label="Cédula"
                    :rules="[requiredValidator, phoneValidator]"
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
                    label="Direccion"
                    :rules="[requiredValidator]"
                  />
                </VCol>
                <!-- 👉 Form Actions -->
                <VCol
                  cols="12"
                  class="d-flex flex-wrap gap-4 justify-center"
                >
                  <VBtn type="submit">
                    Guardar Cambios
                  </VBtn>
                </VCol>
              </VRow>
            </VCardText>
          </VForm>
        </VCard>      
      </VDialog> 
    </VRow>
  </section>
</template>

<style lang="scss">
  .v-list-item-title {
    white-space: normal;
  }
</style>
