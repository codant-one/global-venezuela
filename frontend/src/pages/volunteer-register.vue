<script setup>

import { requiredValidator } from '@validators'
import { useThemesStores } from '@/stores/useThemes'
import { useCommunityCouncilsStores } from '@/stores/useCommunityCouncils'
import { useCircuitsStores } from '@/stores/useCircuits'
import { useStatesStores } from '@/stores/useStates'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import background from '@images/volunteer-people.jpg'
import registerMultistepIllustrationDark from '@images/volunteer-logo-2.png'
import registerMultistepIllustrationLight from '@images/volunteer-logo-2.png'
import registerMultistepBgDark from '@images/pages/register-multistep-bg-dark.png'
import registerMultistepBgLight from '@images/pages/register-multistep-bg-light.png'

const registerMultistepBg = useGenerateImageVariant(registerMultistepBgLight, registerMultistepBgDark)

const currentStep = ref(0)
const registerMultistepIllustration = useGenerateImageVariant(registerMultistepIllustrationLight, registerMultistepIllustrationDark)

const themesStores = useThemesStores()
const circuitsStores = useCircuitsStores()
const statesStores = useStatesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()

const isRequestOngoing = ref(true)

const listThemes = ref([])
const theme_id = ref(null)

const listStates = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])
const listCircuits = ref([])

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])
const listCircuitsByParishes = ref([])

const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const circuit_id = ref('')
const circuitOld_id = ref('')

const isMobile = /Mobi/i.test(navigator.userAgent);

const radioContent = [
  // {
  //     icon: {
  //        icon: 'mdi-flag-variant',
  //        size: isMobile ? '30' : '60',
  //     },
  //     title: 'Nacional',
  //     value: '0'
  // },
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
    subtitle: 'Completa tus datos',
    icon: 'tabler-file-text',
  },
]

const form = ref({
  username: '',
  email: '',
  password: '',
  confirmPassword: '',
  link: '',
  firstName: '',
  lastName: '',
  mobile: '',
  pincode: '',
  address: '',
  landmark: '',
  city: '',
  state: null,
  type: '0',
  cardNumber: '',
  cardName: '',
  expiryDate: '',
  cvv: '',
})

const loadThemes = () => {
  listThemes.value = themesStores.getThemes
}


const loadStates = () => {
  listStates.value = statesStores.getStates
}

const loadMunicipalities = () => {
  listMunicipalities.value = municipalitiesStores.getMunicipalities
}

const loadParishes = () => {
  listParishes.value = parishesStores.getParishes
}

const loadCircuits = () => {
  listCircuits.value = circuitsStores.getCircuits
}

watchEffect(fetchData)

async function fetchData() {

   isRequestOngoing.value = true
   
   if(listParishes.value.length === 0) {
      await themesStores.fetchThemes();
      await statesStores.fetchStates();
      await municipalitiesStores.fetchMunicipalities();
      await parishesStores.fetchParishes();
      await circuitsStores.fetchCircuits({ limit: -1 });

      loadStates()
      loadMunicipalities()
      loadParishes()
      loadCircuits()
      loadThemes()
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
  return listCircuitsByParishes.value.map((parish) => {
    return {
      title: parish.name,
      value: parish.id,
    }
  })
})

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state)
    state_id.value = _state.name
 
    municipality_id.value = ''

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

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
 
    circuit_id.value = ''

    listCircuitsByParishes.value = listCircuits.value.filter(item => item.parish_id === _parish.id)

  }
}

const onSubmit = () => {

  // eslint-disable-next-line no-alert
  alert('Voluntario Registrado..!!')
}
</script>

<template>
  <VRow
    no-gutters
    class="auth-wrapper"
  >
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
      :style="`background: url(${background});`"
    >
      <!-- here your illustration -->
      <div class="d-flex justify-center align-center w-100 position-relative">
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
      class="auth-card-v2 d-flex align-center justify-center px-7 pb-7 px-md-10 bg-gray backgroundMobile"
    >

    <div class="d-block justify-center align-center w-100 position-relative">
      <div class="px-7 py-5 d-block d-md-none">
        <VImg
          :src="registerMultistepIllustration"
        />
      </div>
    
      <VCard
        flat
        class="pa-5 pa-md-10"
      >
        <AppStepper
          v-model:current-step="currentStep"
          :items="items"
          direction="horizontal"
          icon-size="24"
          class="stepper-icon-step-bg mb-8"
        />

        <VWindow
          v-model="currentStep"
          class="disable-tab-transition"
        >
          <VForm>
            <VWindowItem>
               <h5 class="text-h5 mb-1">
                  Voluntariados
               </h5>
               <p class="text-sm">
                  Selecciona el tipo de voluntariado
               </p>

               <CustomRadiosWithIcon
                  v-model:selected-radio="form.type"
                  :radio-content="radioContent"
                  :grid-column="{ sm: '4', cols: '12' }"
               />
            </VWindowItem>
            <VWindowItem>
              <h5 class="text-h5 mb-1">
               Transformación
              </h5>
              <p class="text-sm">
                Selecciona la transformación
              </p>

              <VRow>
                <VCol
                  cols="12"
                  md="6"
                  v-if="Number(form.type) >= 1"
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
                  v-if="Number(form.type) >= 2"
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
                  v-if="Number(form.type) > 2"
                >
                  <VAutocomplete
                     v-model="parish_id"
                     label="Parroquia"
                     :rules="[requiredValidator]"
                     :items="getParishes"
                     :menu-props="{ maxHeight: '200px' }"
                     @update:model-value="selectParishes"
                  />
                </VCol>

                <VCol
                  cols="12"
                  md="6"
                  v-if="Number(form.type) === 3"
                >
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
                     item-value="name"
                     :menu-props="{ maxHeight: '200px' }"
                  />
                </VCol>
              </VRow>
            </VWindowItem>

            <VWindowItem>
              <h5 class="text-h5 mb-1">
               Información Personal
              </h5>
              <p class="text-sm">
               Ingrese su información personal
              </p>


              <VRow>
                <VCol
                  cols="12"
                  md="6"
                >
                  <AppTextField
                    v-model="form.firstName"
                    label="Nombre"
                    placeholder="Nombre"
                  />
                </VCol>

                <VCol
                  cols="12"
                  md="6"
                >
                  <AppTextField
                    v-model="form.lastName"
                    label="Apellido"
                    placeholder="Apellido"
                  />
                </VCol>

                <VCol
                  cols="12"
                  md="6"
                >
                  <AppTextField
                    v-model="form.mobile"
                    type="text"
                    label="Teléfono"
                    placeholder="Teléfono"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="6"
                >
                  <AppTextField
                    v-model="form.cardNumber"
                    type="text"
                    label="Documento"
                    placeholder="Documento"
                  />
                </VCol>
                <VCol
                  cols="12"
                  md="12"
                >
                  <AppTextField
                    v-model="form.city"
                    label="E-mail"
                    placeholder="E-mail"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
          </VForm>
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
            @click="onSubmit"
          >
            Enviar
          </VBtn>

          <VBtn
            v-else
            @click="currentStep++"
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
      </div>
    </VCol>
  </VRow>
</template>

<style lang="scss">
   @use "@core/scss/template/pages/page-auth.scss";

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

   @media (max-width: 768px) {
    .backgroundMobile {
        background-image: url('@images/volunteer-people.jpg');
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