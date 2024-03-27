<script setup>

import { emailValidator, requiredValidator, phoneValidator } from '@/@core/utils/validators'
import { useMiscellaneousStores } from '@/stores/useMiscellaneous'
import { useVolunteersStores } from '@/stores/useVolunteers'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import background from '@images/volunteer-background.jpg'
import registerMultistepIllustrationDark from '@images/volunteer-logo-4.png'
import registerMultistepIllustrationLight from '@images/volunteer-logo-4.png'
import HeroSection from "@/components/register/hero-section.vue";

const registerMultistepIllustration = useGenerateImageVariant(registerMultistepIllustrationLight, registerMultistepIllustrationDark)

const miscellaneousStores = useMiscellaneousStores()
const volunteersStores = useVolunteersStores()

const isRequestOngoing = ref(true)

const listThemes = ref([])
const theme_id = ref('')

const listStates = ref([])
const listMunicipalities = ref([])
const listCircuits = ref([])
const listParishes = ref([])
const listCommunityCouncils = ref([])
const listProfessions = ref([])
const listInstructionDegrees = ref([])

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])
const listCircuitsByMunicipalities = ref([])
const listCommunityCouncilsByCircuits = ref([])

const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const circuit_id = ref('')
const circuitOld_id = ref('')
const community_council_id = ref('')
const profession_id = ref('')
const instruction_degree_id = ref('')

const isTonalSnackbarVisible = ref(false)
const message = ref('')
const color = ref('')

const isRegister = ref(false)

const advisor = ref({
  type: '',
  message: '',
  show: false
})

const isFormValid = ref(false)
const refForm = ref()
const panel = ref([0])

const form = ref({ name: null, document: null, phone: null, email: null, address: null})
const responsible = ref({ name: null, document: null, phone: null, email: null })
const instagram = ref(null)
const facebook = ref(null)
const twitter = ref(null)

const loadData = () => {
    listThemes.value = miscellaneousStores.getData.themes
    listStates.value = miscellaneousStores.getData.states
    listCircuits.value = miscellaneousStores.getData.circuits
    listMunicipalities.value = miscellaneousStores.getData.municipalities
    listParishes.value = miscellaneousStores.getData.parishes
    listCommunityCouncils.value = miscellaneousStores.getData.communityCouncils
    listProfessions.value = miscellaneousStores.getData.professions
    listInstructionDegrees.value = miscellaneousStores.getData.instructionDegrees

    let newTheme = {id: 8, name: 'OTRO'};

    listThemes.value.unshift(newTheme);
}

watchEffect(fetchData)

async function fetchData() {

   isRequestOngoing.value = true
   
   if(listMunicipalities.value.length === 0) {
      await miscellaneousStores.fetchData({communityCouncil: true});
      loadData()
   }

   isRequestOngoing.value = false
}

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

const getCircuits = computed(() => {
  return listCircuitsByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

const getCommunityCouncils = computed(() => {
  return listCommunityCouncilsByCircuits.value.map((circuit) => {
    return {
      title: circuit.name,
      value: circuit.id,
    }
  })
})

const clearState = () => {
  state_id.value = ''
  stateOld_id.value = ''
  municipality_id.value = ''
  municipalityOld_id.value = ''
  parish_id.value = ''
  parishOld_id.value = ''
  circuit_id.value = ''
  circuitOld_id.value = ''
  community_council_id.value = ''

  listMunicipalitiesByStates.value = []
  listParishesByMunicipalities.value = []
  listCircuitsByMunicipalities.value = []
  listCommunityCouncilsByCircuits.value = []
}

const clearMunicipality = () => {
  municipality_id.value = ''
  municipalityOld_id.value = ''
  parish_id.value = ''
  parishOld_id.value = ''
  circuit_id.value = ''
  circuitOld_id.value = ''
  community_council_id.value = ''

  listParishesByMunicipalities.value = []
  listCircuitsByMunicipalities.value = []
  listCommunityCouncilsByCircuits.value = []
}

const clearParish = () => {
  parish_id.value = ''
  parishOld_id.value = ''
}

const clearCircuit = () => {
  community_council_id.value = ''

  listCommunityCouncilsByCircuits.value = []
}

const clearCommunityCouncil = () => {
  community_council_id.value = ''
}

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state)
    state_id.value = _state.name
    stateOld_id.value = _state.id
    municipality_id.value = ''

    listMunicipalitiesByStates.value = listMunicipalities.value.filter(item => item.state_id === _state.id)
  }
}

const selectMunicipalities = municipality => {
  if (municipality) {
    let _municipality = listMunicipalities.value.find(item => item.id === municipality)
    municipality_id.value = _municipality.name
    municipalityOld_id.value = _municipality.id
    parish_id.value = ''
    circuit_id.value = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)
    listCircuitsByMunicipalities.value = listCircuits.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
    parishOld_id.value = _parish.id
  }
}

const selectCircuit = circuit => {
  if (circuit) {
    let _circuit = listCircuits.value.find(item => item.id === circuit)
    circuit_id.value = _circuit.name
    circuitOld_id.value = _circuit.id

    community_council_id.value = ''
    listCommunityCouncilsByCircuits.value = listCommunityCouncils.value.filter(item => item.circuit_id === _circuit.id)

  }
}

const onSubmit = () => {

  panel.value = [0, 1, 2, 3, 4, 5, 6, 7]

  refForm.value?.validate().then(({ valid }) => {

    if(valid) {

        let data = {
            responsible: responsible.value,
            form: [form.value],
            type: '4',
            theme_id: theme_id.value,
            state_id: stateOld_id.value,
            municipality_id: municipalityOld_id.value,
            circuit_id: circuitOld_id.value,
            parish_id: parishOld_id.value,
            community_council_id: community_council_id.value,
            profession_id: profession_id.value,
            instruction_degree_id: instruction_degree_id.value,
            instagram: instagram.value,
            facebook: facebook.value,
            twitter: twitter.value
        }

        console.log('data', data)

        volunteersStores.register(data)
            .then((res) => {
                if (res.data.success) {
                    isTonalSnackbarVisible.value = true
                    message.value = 'Voluntario registrado con exito!!'
                    color.value = 'primary'

                    setTimeout(() => {
                    window.location.reload();
                    }, 3000)

                } else {
                    isTonalSnackbarVisible.value = true
                    message.value = 'Ha ocurrido un error'
                    color.value = 'error'
                }
            })
            .catch((err) => {

                if(err.success === false && err.feedback === 'params_validation_failed') {

                    advisor.value.show = true
                    advisor.value.type = 'error'
                    advisor.value.message = Object.values(err.message).flat().join('<br>');
                    

                    setTimeout(() => {
                    advisor.value.show = false
                    advisor.value.type = ''
                    advisor.value.message = ''
                    }, 10000)
                } else {
                    isTonalSnackbarVisible.value = true
                    message.value = err
                    color.value = 'error'
                }
            })
    }

  })
}

const register = () => {
    isRegister.value = true
}

</script>

<template>
    <section>
        <!-- 👉 Hero Section  -->
        <HeroSection
            v-if="!isRegister"
            @register="register" />

        <VRow
            v-else
            no-gutters
            class="auth-wrapper"
        >
            <VSnackbar
                v-model="isTonalSnackbarVisible"
                location="top"
                variant="flat"
                :color="color"
            >
                {{ message }}
            </VSnackbar>
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
                md="4"
                class="d-none d-md-flex"
                :style="`background: url(${background}); background-repeat: no-repeat; background-size: cover`"
            >
                <!-- here your illustration -->
                <div class="d-flex justify-center align-center w-100 position-relative pa-16">
                    <VImg
                        :src="registerMultistepIllustration"
                        class="illustration-image"
                    />
                    <!-- <VImg
                    :src="registerMultistepBg"
                    class="bg-image position-absolute w-100"
                    /> -->
                </div>
            </VCol>

            <VCol
                cols="12"
                md="8"
                class="auth-card-v2 d-flex align-center justify-center px-7 pb-7 pt-0 pt-md-7 px-md-10 bg-gray backgroundMobile"
            >

                <div class="d-block justify-center align-center w-100 position-relative">
                    <div class="px-7 py-0 d-block d-md-none">
                        <VImg
                            :src="registerMultistepIllustration"
                        />
                    </div>
                
                    <VForm
                        ref="refForm"
                        v-model="isFormValid"
                        @submit.prevent="onSubmit">
                        <VCard
                            flat
                            class="pa-5"
                        >
                            <v-alert
                                v-if="advisor.show"
                                :type="advisor.type"
                                class="mb-6"
                                icon="mdi-alert-circle-outline"
                                prominent
                                closable
                                >
                                <span v-html="advisor.message" />
                            </v-alert>

                            <VCardTitle class="text-h5 mb-2 px-2 px-md-10">
                                Registro Individual de Voluntareado
                            </VCardTitle>
                               
                            <VCardText class="px-2 px-md-10 pb-0">
                                <VRow>
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
                                            @click:clear="clearState"
                                            clearable
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
                                            @click:clear="clearMunicipality"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="parish_id"
                                            label="Parroquia"
                                            :rules="[requiredValidator]"
                                            :items="getParishes"
                                            :menu-props="{ maxHeight: '200px' }"
                                            @update:model-value="selectParishes"
                                            @click:clear="clearParish"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="circuit_id"
                                            label="Circuito Comunal"
                                            :items="getCircuits"
                                            :menu-props="{ maxHeight: '200px' }"
                                            @update:model-value="selectCircuit"
                                            @click:clear="clearCircuit"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="community_council_id"
                                            label="Consejo Comunal"
                                            :items="getCommunityCouncils"
                                            :menu-props="{ maxHeight: '200px' }"
                                            @click:clear="clearCommunityCouncil"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="theme_id"
                                            label="Transformación"
                                            :rules="[requiredValidator]"
                                            :items="listThemes"
                                            item-title="name"
                                            item-value="id"
                                            :menu-props="{ maxHeight: '200px' }"
                                            clearable
                                    />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="profession_id"
                                            label="Profesión"
                                            :items="listProfessions"
                                            item-title="name"
                                            item-value="id"
                                            :menu-props="{ maxHeight: '200px' }"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VAutocomplete
                                            v-model="instruction_degree_id"
                                            label="Grado de Instrucción"
                                            :items="listInstructionDegrees"
                                            item-title="name"
                                            item-value="id"
                                            :menu-props="{ maxHeight: '200px' }"
                                            clearable
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6"> 
                                        <VTextField
                                            v-model="form.name"
                                            label="Nombre"
                                            placeholder="Nombre"
                                            :rules="[requiredValidator]"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VTextField
                                            v-model="form.document"
                                            type="tel"
                                            label="Cédula"
                                            placeholder="Cédula"
                                            :rules="[phoneValidator, requiredValidator]"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VTextField
                                            type="tel"
                                            v-model="form.phone"
                                            label="Teléfono"
                                            placeholder="+(XX) XXXXXXXXX"
                                            :rules="[phoneValidator, requiredValidator]"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="6">
                                        <VTextField
                                            v-model="form.email"
                                            label="E-mail"
                                            type="email"
                                            :rules="[emailValidator]"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="12">
                                        <VTextarea
                                            v-model="form.address"
                                            rows="3"
                                            label="Dirección"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="4"> 
                                        <VTextField
                                            v-model="instagram"
                                            label="Instagram"
                                            placeholder="Instagram"
                                            prepend-inner-icon="mdi-instagram"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="4"> 
                                        <VTextField
                                            v-model="facebook"
                                            label="Facebook"
                                            placeholder="Facebook"
                                            prepend-inner-icon="mdi-facebook"
                                        />
                                    </VCol>
                                    <VCol cols="12" md="4"> 
                                        <VTextField
                                            v-model="twitter"
                                            label="twitter"
                                            placeholder="twitter"
                                            prepend-inner-icon="mdi-twitter"
                                        />
                                    </VCol>
                                </VRow>
                            </VCardText>                      
                            <VCardText class="px-2 px-md-10">
                                <div class="d-flex flex-wrap justify-sm-space-between justify-center gap-x-4 gap-y-2 mt-8">
                                    <VSpacer />
                                    <VBtn
                                        color="primary"
                                        append-icon="tabler-check"
                                        type="submit"
                                    >
                                        Enviar
                                    </VBtn>
                                </div>
                            </VCardText>
                        </VCard>
                    </VForm>
                </div>
            </VCol>
        </VRow>
    </section>
</template>

<style lang="scss">
  @use "@core/scss/template/pages/page-auth.scss";

  
    .v-application__wrap {
        min-height: 100% !important;
    }

    .v-theme--light {
        --v-theme-primary: 8, 113, 76 !important;
        --v-theme-error: 230, 48, 34 !important;
    }

    .illustration-image {
        block-size: 550px;
        inline-size: 248px;
    }

    .bg-image {
        inset-block-end: 0;
    }

    .bg-gray {
        background-color: #fafafa;
    }

    .v-text-field .v-input__details {
        padding-inline-start: 4px !important;
        padding-top: 2px !important;
        min-height: 18px !important;
    }

    @media (max-width: 768px) {

        .v-btn--size-default {
            font-size: 13px;
        }

        .v-card-title {
            text-overflow: clip;
            font-size: 1rem !important;
        }

        .backgroundMobile {
            background-image: url('@images/volunteer-background.jpg');
            background-repeat: no-repeat !important;
        }
    }

</style>

<route lang="yaml">
    meta:
      layout: blank
      action: ver
      subject: Auth
      redirectIfLoggedIn: false
</route>