<script setup>

import { emailValidator, requiredValidator } from '@/@core/utils/validators'
import { useUsersStores } from '@/stores/useUsers'
import { useStatesStores } from '@/stores/useStates'
import { useCitiesStores } from '@/stores/useCities'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'
import { useGendersStores } from '@/stores/useGenders'

const props = defineProps({
  rolesList: {
    type: Object,
    required: true
  }

})

const emit = defineEmits([
  'close',
  'alert',
  'data'
])

const usersStores = useUsersStores()
const statesStores = useStatesStores()
const citiesStores = useCitiesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()
const gendersStores = useGendersStores()

const listStates = ref([])
const listCities = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])
const listGenders = ref([])

const listCitiesByStates = ref([])
const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])

const refFormCreate = ref()

const isUserCreateDialog = ref(false)
const isPasswordVisible = ref(false)
const email = ref('')
const name = ref('')
const password = ref('')
const last_name = ref('')
const phone = ref('----')
const address = ref('----')
const document = ref('----')
const gender_id = ref('Masculino')
const genderOld_id = ref(1)
const state_id = ref('')
const city_id = ref('')
const municipality_id = ref('')
const parish_id = ref('')
const assignedRoles = ref([])

const advisor = ref({
  type: '',
  message: '',
  show: false
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

})


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

const closeUserCreateDialog  = function(){
  isUserCreateDialog.value = false

  nextTick(() => {
    refFormCreate.value?.reset()
    refFormCreate.value?.resetValidation()
    email.value = ''
    name.value = ''
    password.value = ''
    last_name.value = ''
    phone.value = '----'
    address.value = '----'
    gender_id.value = 'Masculino'
    document.value = '----'
    assignedRoles.value = []
  })
}

const onSubmitCreate = () => {
  refFormCreate.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {
      
      let data = {
        name: name.value,
        email: email.value,
        password: password.value,
        last_name: last_name.value,
        parish_id: parish_id.value,
        city_id: city_id.value,
        roles: assignedRoles.value
      }

      usersStores.addUser(data)
        .then(response => {
          closeUserCreateDialog()

          window.scrollTo(0, 0)
          
          advisor.value.show = true
          advisor.value.type = 'success'
          advisor.value.message = 'Usuario Creado!'
          
          emit('alert', advisor)
          emit('data')
          emit('close')

          nextTick(() => {
            refFormCreate.value?.reset()
            refFormCreate.value?.resetValidation()
            email.value = ''
            name.value = ''
            password.value = ''
            last_name.value = ''
            phone.value = '----'
            address.value = '----'
            gender_id.value = 'Masculino'
            document.value = '----'
            assignedRoles.value = []
          })

          setTimeout(() => {
            advisor.value.show = false
            advisor.value.type = ''
            advisor.value.message = ''
            emit('alert', advisor)
          }, 5000)
        }).catch(error => {

          closeUserCreateDialog()
          window.scrollTo(0, 0)

          advisor.value.show = true
          advisor.value.type = 'error'
          
          if (error.feedback === 'params_validation_failed') {
            if(error.message.hasOwnProperty('email'))
              advisor.value.message = error.message.email[0]
            else if(error.message.hasOwnProperty('username'))
              advisor.value.message = error.message.username[0]
          } else {
            advisor.value.message = 'Se ha producido un error...! (Server Error)'
          }

          emit('alert', advisor)
          emit('data')
          emit('close')

          setTimeout(() => {
            advisor.value.show = false
            advisor.value.type = ''
            advisor.value.message = ''
            emit('alert', advisor)
          }, 5000)   
        })
    }
  })
}

</script>

<template>
  <!-- 👉 CREATE USER -->
  <VDialog
    v-model="isUserCreateDialog"
    max-width="600"
    persistent
    >
    <template #activator="{ props }">
      <VBtn
        v-if="$can('crear','usuarios')"
        v-bind="props"
        prepend-icon="tabler-plus"
        >
        Crear usuario
      </VBtn>
    </template>

    <DialogCloseBtn @click="closeUserCreateDialog " />

    <VCard title="Crear usuario">
      <VDivider class="mt-4"/>
      <VCardText>
        <VForm
          ref="refFormCreate"
          @submit.prevent="onSubmitCreate"
          >
          <VCardText>
            <VRow>
              <VCol md="6" cols="12">
                <VTextField
                  v-model="name"
                  label="Nombres"
                  :rules="[requiredValidator]"
                  />
              </VCol>
              <VCol md="6" cols="12">
                <VTextField
                  v-model="last_name"
                  label="Apellidos"
                  :rules="[requiredValidator]"
                />
              </VCol>
              <VCol cols="12" md="12">
                <VTextField
                  v-model="email"
                  label="E-mail"
                  type="email"
                  :rules="[requiredValidator,emailValidator]"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="password"
                  label="Contraseña"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :rules="[requiredValidator]"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>
              <VCol cols="12" md="6" >
                <VTextField
                  v-model="phone"
                  type="tel"
                  label="Teléfono"
                  placeholder="+(XX) XXXXXXXXX"
                  disabled
                />
              </VCol>
              <VCol cols="12" md="6">
                <VAutocomplete
                  v-model="gender_id"
                  label="Género"
                  :items="listGenders"
                  item-title="name"
                  item-value="id"
                  :menu-props="{ maxHeight: '200px' }"
                  disabled
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="document"
                  type="tel"
                  label="Cédula"
                  disabled
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
              <VCol cols="12" md="6">
                <VAutocomplete
                  v-model="city_id"
                  label="Ciudad"
                  :rules="[requiredValidator]"
                  :items="getCities"
                  :menu-props="{ maxHeight: '200px' }"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VAutocomplete
                  v-model="municipality_id"
                  label="Municipio"
                  :rules="[requiredValidator]"
                  :items="getMunicipalities"
                  :menu-props="{ maxHeight: '200px' }"
                  @update:model-value="selectMunicipalities"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VAutocomplete
                  v-model="parish_id"
                  label="Parroquia"
                  :rules="[requiredValidator]"
                  :items="getParishes"
                  :menu-props="{ maxHeight: '200px' }"
                />
              </VCol>
              <VCol cols="12" md="12">
                <VTextarea
                  v-model="address"
                  rows="3"
                  label="Dirección"
                  disabled
                />
              </VCol>
              <VCol cols="12">
                <VCombobox
                  v-model="assignedRoles"
                  chips
                  clearable
                  multiple
                  closable-chips
                  clear-icon="tabler-circle-x"
                  :items="rolesList"
                  label="Roles asignados al usuario"
                  :rules="[requiredValidator]"
                 />
              </VCol>
            </VRow>
            <VCardText class="d-flex justify-end gap-3 flex-wrap px-0">
              <VBtn
                color="secondary"
                variant="tonal"
                @click="closeUserCreateDialog"
              >
                Cancelar
              </VBtn>
              <VBtn type="submit">
                Crear
              </VBtn>
            </VCardText>
          </VCardText>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>
<route lang="yaml">
  meta:
    action: crear
    subject: usuarios
</route>
