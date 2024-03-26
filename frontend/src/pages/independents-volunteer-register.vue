<script setup>

import { emailValidator, requiredValidator, phoneValidator } from '@/@core/utils/validators'
import { useMiscellaneousStores } from '@/stores/useMiscellaneous'
import { useVolunteersStores } from '@/stores/useVolunteers'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import background from '@images/volunteer-background.jpg'
import registerMultistepIllustrationDark from '@images/volunteer-logo-4.png'
import registerMultistepIllustrationLight from '@images/volunteer-logo-4.png'
import HeroSection from "@/components/register/hero-section.vue";

const currentStep = ref(0)
const registerMultistepIllustration = useGenerateImageVariant(registerMultistepIllustrationLight, registerMultistepIllustrationDark)

const miscellaneousStores = useMiscellaneousStores()
const volunteersStores = useVolunteersStores()

const isRequestOngoing = ref(true)

const listThemes = ref([])
const theme_id = ref(null)

const listStates = ref([])
const listMunicipalities = ref([])
const listCircuits = ref([])

const listMunicipalitiesByStates = ref([])
const listCircuitsByMunicipalities = ref([])

const state_id = ref(null)
const stateOld_id = ref(null)
const municipality_id = ref(null)
const municipalityOld_id = ref(null)
const circuit_id = ref(null)

const isTonalSnackbarVisible = ref(false)
const message = ref('')
const color = ref('')
const isMobile = /Mobi/i.test(navigator.userAgent);

const isRegister = ref(false)

const advisor = ref({
  type: '',
  message: '',
  show: false
})

const radioContent = [
  {
      icon: {
         icon: 'mdi-map-marker-radius',
         size: isMobile ? '30' : '60',
      },
      title: 'Estatal',
      value: '1'
  },
  {
      icon: {
         icon: 'mdi-city',
         size: isMobile ? '30' : '60',
      },
      title: 'Municipal',
      value: '2'
  },
  {
      icon: {
         icon: 'mdi-crosshairs-gps',
         size: isMobile ? '30' : '60',
      },
      title: 'Por Circuitos Comunales',
      value: '3'
  }
]

const items = [
  {
    title: 'Voluntarios',
    subtitle: 'Tipos',
    icon: 'tabler-heart-handshake',
  },
  {
    title: 'Transformación',
    subtitle: 'Selecciona la transformación',
    icon: 'tabler-book',
  },
  {
    title: 'Información',
    subtitle: 'Completa los datos',
    icon: 'tabler-file-text',
  },
]

const isFormValid = ref(false)
const refForm = ref()
const panel = ref([0])

const type = ref('1')
const form = ref({ name: null, document: null, phone: null, email: null})
const responsible = ref({ name: null, document: null, phone: null, email: null })

const loadData = () => {
  listThemes.value = miscellaneousStores.getData.themes
  listStates.value = miscellaneousStores.getData.states
  listCircuits.value = miscellaneousStores.getData.circuits
  listMunicipalities.value = miscellaneousStores.getData.municipalities

  let newTheme = {id: 8, name: 'DE CARÁCTER INDIVIDUAL'};

  listThemes.value.unshift(newTheme);
}

watchEffect(fetchData)

async function fetchData() {

   isRequestOngoing.value = true
   
   if(listMunicipalities.value.length === 0) {
      await miscellaneousStores.fetchData();
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

const getCircuits = computed(() => {
  return listCircuitsByMunicipalities.value.map((municipality) => {
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
    circuit_id.value = ''

    listCircuitsByMunicipalities.value = listCircuits.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const onSubmit = () => {

  panel.value = [0, 1, 2, 3, 4, 5, 6, 7]

  refForm.value?.validate().then(({ valid }) => {
    if (currentStep.value === 0) {
      currentStep.value++
    } else if(!valid && currentStep.value === 1 && type.value === '1' && refForm.value.items.length === 7 && refForm.value.errors.length <= 3) {
      currentStep.value++
    } else if(!valid && currentStep.value === 1 && type.value === '2' && refForm.value.items.length === 8 && refForm.value.errors.length <= 4) {
      currentStep.value++
    } else if(!valid && currentStep.value === 1 && type.value === '3' && refForm.value.items.length === 9 && refForm.value.errors.length <= 5){
      currentStep.value++
    } else if (valid && currentStep.value === 1) {
      currentStep.value++
    } else if (valid && currentStep.value === 2) {

      let data = {
        responsible: responsible.value,
        form: [form.value],
        type: type.value,
        theme_id: theme_id.value,
        state_id: stateOld_id.value,
        municipality_id: municipalityOld_id.value,
        circuit_id: circuit_id.value
      }

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
                class="auth-card-v2 d-flex align-center justify-center px-7 pb-7 pt-7 px-md-10 bg-gray backgroundMobile"
            >

                <div class="d-block justify-center align-center w-100 position-relative">
                    <div class="px-7 py-5 d-block d-md-none">
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
                            class="pa-5 pa-md-10"
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

                            <AppStepper
                                v-model:current-step="currentStep"
                                :items="items"
                                :isActiveStepValid="false"
                                direction="horizontal"
                                icon-size="24"
                                class="stepper-icon-step-bg mb-8"
                            />

                            <VWindow
                                v-model="currentStep"
                                class="disable-tab-transition"
                            >
                                <VWindowItem>
                                <h5 class="text-h5 mb-2">
                                    Voluntariados
                                </h5>
                                <p class="text-sm">
                                    Selecciona el tipo de voluntariado
                                </p>

                                <CustomRadiosWithIcon
                                    v-model:selected-radio="type"
                                    :radio-content="radioContent"
                                    :grid-column="{ sm: '4', cols: '12' }"
                                />
                                </VWindowItem>
                                <VWindowItem>
                                <h5 class="text-h5 mb-2">
                                    Transformación
                                </h5>
                                <p class="text-sm">
                                    Selecciona la transformación
                                </p>
                                <VRow>
                                    <VCol cols="12" md="6" v-if="Number(type) >= 1">
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
                                    <VCol cols="12" md="6" v-if="Number(type) >= 2">
                                    <VAutocomplete
                                        v-model="municipality_id"
                                        label="Municipio"
                                        :rules="[requiredValidator]"
                                        :items="getMunicipalities"
                                        :menu-props="{ maxHeight: '200px' }"
                                        @update:model-value="selectMunicipalities"
                                    />
                                    </VCol>
                                    <VCol cols="12" md="6" v-if="Number(type) === 3">
                                    <VAutocomplete
                                        v-model="circuit_id"
                                        label="Circuito Comunal"
                                        :rules="[requiredValidator]"
                                        :items="getCircuits"
                                        :menu-props="{ maxHeight: '200px' }"
                                    />
                                    </VCol>
                                    <VCol cols="12">
                                    <VAutocomplete
                                        v-model="theme_id"
                                        label="Transformación"
                                        :rules="[requiredValidator]"
                                        :items="listThemes"
                                        item-title="name"
                                        item-value="id"
                                        :menu-props="{ maxHeight: '200px' }"
                                    />
                                    </VCol>
                                </VRow>
                                </VWindowItem>
                                <VWindowItem>
                                    <h5 class="text-h5 mb-2">
                                        Información Personal
                                    </h5>
                                    <p class="text-sm">
                                        Ingrese la información personal de los voluntarios
                                    </p>
                                    <VCard>
                                        <VCardTitle>
                                            Voluntario
                                        </VCardTitle>
                                        <VCardText>
                                            <VRow no-gutters>
                                                <VCol cols="12" md="12"> 
                                                    <VTextField
                                                        v-model="form.name"
                                                        label="Nombre"
                                                        placeholder="Nombre"
                                                        :rules="[requiredValidator]"
                                                        class="mb-2"
                                                        />
                                                    </VCol>
                                                <VCol cols="12" md="6">
                                                    <VTextField
                                                        v-model="form.document"
                                                        type="tel"
                                                        label="Cédula"
                                                        placeholder="Cédula"
                                                        :rules="[phoneValidator, requiredValidator]"
                                                        class="mb-2 me-3"
                                                    />
                                                </VCol>
                                                <VCol cols="12" md="6">
                                                    <VTextField
                                                        type="tel"
                                                        v-model="form.phone"
                                                        label="Teléfono"
                                                        placeholder="+(XX) XXXXXXXXX"
                                                        :rules="[phoneValidator, requiredValidator]"
                                                        class="mb-2"
                                                    />
                                                </VCol>
                                                <VCol cols="12" md="12">
                                                    <VTextField
                                                        v-model="form.email"
                                                        label="E-mail"
                                                        type="email"
                                                        :rules="[emailValidator]"
                                                    />
                                                </VCol>
                                            </VRow>
                                        </VCardText>
                                    </VCard>                                
                                </VWindowItem>
                            </VWindow>

                            <div class="d-flex flex-wrap justify-sm-space-between justify-center gap-x-4 gap-y-2 mt-8">
                                <VBtn
                                v-if="currentStep > 0"
                                color="secondary"
                                :disabled="currentStep === 0"
                                variant="tonal"
                                @click="currentStep--"
                                >
                                <VIcon
                                    icon="tabler-arrow-left"
                                    start
                                    class="flip-in-rtl"
                                />
                                Atrás
                                </VBtn>

                                <VSpacer />
                                <VBtn
                                v-if="items.length - 1 === currentStep"
                                color="primary"
                                append-icon="tabler-check"
                                type="submit"
                                >
                                Enviar
                                </VBtn>

                                <VBtn
                                v-else
                                type="submit"
                                >
                                Siguiente

                                <VIcon
                                    icon="tabler-arrow-right"
                                    end
                                    class="flip-in-rtl"
                                />
                                </VBtn>
                            </div>
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