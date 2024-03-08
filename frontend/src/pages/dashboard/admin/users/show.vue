<script setup>

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
  'close'
])

const listStates = ref(props.states)
const listCities = ref(props.cities)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listGenders = ref(props.genders)

const email = ref('')
const name = ref('')
const password = ref('')
const last_name = ref('')
const phone = ref('')
const isPhone = ref(false)
const address = ref('')
const isAddress = ref(false)
const document = ref('')
const isDocument = ref(false)
const gender_id = ref('')
const genderOld_id = ref('')
const isGender = ref(false)
const state_id = ref('')
const stateOld_id = ref('')
const city_id = ref('')
const cityOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const assignedRoles = ref([])

onMounted(async () => {

  
})

watchEffect(() => {
    if (props.isDrawerOpen) {

        if (!(Object.entries(props.user).length === 0) && props.user.constructor === Object) {
            email.value = props.user.email
            password.value = props.user.password
            name.value = props.user.name
            last_name.value = props.user.last_name
            phone.value = props.user.user_detail?.phone ?? '----'
            isPhone.value = (props.user.user_detail?.phone === null) ? true : false
            address.value = props.user.user_detail?.address ?? '----'
            isAddress.value = (props.user.user_detail?.address === null) ? true : false
            document.value = props.user.user_detail?.document ?? '----'
            isDocument.value = (props.user.user_detail?.document === null) ? true : false

            // console.log('aa',  props.user)
            genderOld_id.value = props.user.user_detail?.gender?.id
            gender_id.value = props.user.user_detail?.gender?.name
            isGender.value = (props.user.user_detail?.gender === null) ? true : false

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
})

const closeUserDetailDialog = function() {
    emit('update:isDrawerOpen', false)
    emit('close')
}

</script>

<template>
    <!-- DIALOGO DE VER -->
    <VDialog
        :model-value="props.isDrawerOpen"
        max-width="600"
        persistent
        >
        <!-- Dialog close btn -->
        <DialogCloseBtn @click="closeUserDetailDialog" />

        <!-- Dialog Content -->
        <VCard title="Detalle usuario">
            <VDivider class="mt-4"/>
            <VCardText>
                <VRow>
                    <VCol md="6" cols="12">
                        <VTextField
                            v-model="name"
                            label="Nombres"
                            readonly
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VTextField
                            v-model="last_name"
                            label="Apellidos"
                            readonly
                        />
                    </VCol>
                    <VCol md="12" cols="12">
                        <VTextField
                            v-model="email"
                            label="E-mail"
                            readonly
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VTextField
                            v-model="phone"
                            type="password"
                            label="Contraseña"
                            readonly
                        />
                    </VCol>                    
                    <VCol md="6" cols="12">
                        <VTextField
                            v-model="phone"
                            type="tel"
                            label="Teléfono"
                            :readonly="!isPhone"
                            :disabled="isPhone"
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VAutocomplete
                            v-model="gender_id"
                            label="Género"
                            :items="listGenders"
                            :readonly="!isGender"
                            :disabled="isGender"
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VTextField
                            v-model="document"
                            type="tel"
                            label="Cédula"
                            :readonly="!isDocument"
                            :disabled="isDocument"
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VAutocomplete
                            v-model="state_id"
                            label="Estado"
                            :items="listStates"
                            readonly
                        />
                       
                    </VCol>
                    <VCol md="6" cols="12">
                        <VAutocomplete
                            v-model="city_id"
                            label="Ciudad"
                            :items="listCities"
                            readonly
                        />
                    </VCol>
                    <VCol md="6" cols="12">
                        <VAutocomplete
                            v-model="municipality_id"
                            label="Municipio"
                            :items="listMunicipalities"
                            readonly
                        />
                    </VCol> 
                    <VCol md="6" cols="12">
                        <VAutocomplete
                            v-model="parish_id"
                            label="Parroquia"
                            :items="listParishes"
                            readonly
                        />
                    </VCol>
                    <VCol cols="12" md="12">
                        <VTextarea
                            v-model="address"
                            rows="3"
                            label="Dirección"
                            :readonly="!isAddress"
                            :disabled="isAddress"
                            />
                    </VCol>               
                    <VCol md="12" cols="12">
                        <VCombobox
                            v-model="assignedRoles"
                            chips
                            multiple
                            :items="rolesList"
                            label="Roles asignados al usuario"
                            readonly
                        />
                    </VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VDialog>
</template>
