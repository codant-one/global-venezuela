<script setup>

import { requiredValidator, phoneValidator } from '@/@core/utils/validators'
import { useUsersStores } from '@/stores/useUsers'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  rolesList: {
    type: Object,
    required: true
  },
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
  }

})

const emit = defineEmits([
  'update:isDrawerOpen',
  'close',
  'alert',
  'data'
])

const usersStores = useUsersStores()

const refFormEdit = ref()

const listStates = ref(props.states)
const listCities = ref(props.cities)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listGenders = ref(props.genders)

const listCitiesByStates = ref([])
const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])

const id = ref('')
const email = ref('')
const name = ref('')
const last_name = ref('')
const phone = ref('')
const address = ref('')
const document = ref('')
const gender_id = ref('')
const genderOld_id = ref('')
const state_id = ref('')
const stateOld_id = ref('')
const city_id = ref('')
const cityOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
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

onMounted(async () => {
  fetchData()
})

watchEffect(fetchData)

async function fetchData() {
  if (props.isDrawerOpen) {
    if (!(Object.entries(props.user).length === 0) && props.user.constructor === Object) {
      id.value = props.user.id
      email.value = props.user.email
      name.value = props.user.name
      last_name.value = props.user.last_name
      phone.value = props.user.user_detail?.phone
      address.value = props.user.user_detail?.address
      document.value = props.user.user_detail?.document
           
      genderOld_id.value = props.user.user_detail?.gender?.id
      gender_id.value = props.user.user_detail?.gender?.name

      stateOld_id.value = props.user.user_detail?.parish.municipality.state.id
      state_id.value = props.user.user_detail?.parish.municipality.state.name

      cityOld_id.value = props.user.user_detail?.city.id
      city_id.value = props.user.user_detail?.city.name

      municipalityOld_id.value = props.user.user_detail?.parish.municipality.id
      municipality_id.value = props.user.user_detail?.parish.municipality.name

      parishOld_id.value = props.user.user_detail?.parish.id
      parish_id.value = props.user.user_detail?.parish.name

      assignedRoles.value = props.user.assignedRoles

    }
  }
}

const closeUserEditDialog = function(){
    emit('update:isDrawerOpen', false)
    emit('close')
}

const onSubmitEdit = () =>{
  refFormEdit.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {

        let data = {
          name: name.value,
          email: email.value,
          last_name: last_name.value,
          phone: phone.value,
          address: address.value,
          document: document.value,
          gender_id: (Number.isInteger(gender_id.value)) ? gender_id.value : genderOld_id.value,
          parish_id:(Number.isInteger(parish_id.value)) ? parish_id.value : parishOld_id.value,
          city_id: (Number.isInteger(city_id.value)) ? city_id.value : cityOld_id.value,
          roles: assignedRoles.value
        }

        usersStores.updateUser(data, id.value)
            .then(response => {
                closeUserEditDialog()

                window.scrollTo(0, 0)

                advisor.value.show = true
                advisor.value.type = 'success'
                advisor.value.message = 'Usuario actualizado!'

                emit('alert', advisor)
                emit('data')
                emit('close')

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
    <!-- DIALOGO DE EDITAR -->
    <VDialog
        :model-value="props.isDrawerOpen"
        max-width="600"
        persistent
        >
        <!-- Dialog close btn -->
        <DialogCloseBtn @click="closeUserEditDialog" />

        <!-- Dialog Content -->
        <VCard title="Editar usuario">
          <VDivider class="mt-4"/>
            <VForm
                ref="refFormEdit"
                @submit.prevent="onSubmitEdit">
                <VCardText>
                    <VRow>
                        <VCol
                          md="6"
                          cols="12"
                        >
                          <VTextField
                            v-model="name"
                            label="Nombres"
                            :rules="[requiredValidator]"
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
                          md="12"
                        >
                          <VTextField
                            v-model="email"
                            label="E-mail"
                            disabled
                          />
                        </VCol>
                        <VCol md="6" cols="12">
                          <VTextField
                            v-model="phone"
                            type="password"
                            label="Contraseña"
                            disabled
                          />
                        </VCol> 
                        <VCol
                          md="6"
                          cols="12"
                        >
                          <VTextField
                            v-model="phone"
                            type="tel"
                            label="Teléfono"
                            placeholder="+(XX) XXXXXXXXX"
                            :rules="[phoneValidator, requiredValidator]"
                          />
                        </VCol>
                        <VCol
                          cols="12"
                          md="6"
                        >
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
                        <VCol
                          cols="12"
                          md="6"
                        >
                          <VTextField
                            v-model="document"
                            type="tel"
                            label="Cédula"
                            :rules="[requiredValidator, phoneValidator]"
                          />
                        </VCol>
                        <VCol
                          cols="12"
                          md="6"
                        >
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
                            @click="closeUserEditDialog"
                        >
                            Cancelar
                        </VBtn>
                        <VBtn type="submit">
                            Editar
                        </VBtn>
                    </VCardText>
                </VCardText>
            </VForm>
        </VCard>
    </VDialog>
</template>
<route lang="yaml">
  meta:
    action: editar
    subject: usuarios
</route>
