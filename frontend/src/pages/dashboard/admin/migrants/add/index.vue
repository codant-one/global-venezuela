<script setup>

import { ref } from "vue"
import { useMigrantsStores } from '@/stores/useMigrants'
import { useMiscellaneousStores } from '@/stores/useMiscellaneous'
import SettingsGeneral from '@/views/apps/migrants/settings/SettingsGeneral.vue'
import SettingsDocument from '@/views/apps/migrants/settings/SettingsDocument.vue'
import SettingsLocation from '@/views/apps/migrants/settings/SettingsLocation.vue'
import SettingsInfoMigrant from '@/views/apps/migrants/settings/SettingsInfoMigrant.vue'
import router from '@/router'

const migrantsStores = useMigrantsStores()
const miscellaneousStores = useMiscellaneousStores()

const emitter = inject("emitter")

const rol = ref(null)
const userDataJ = ref('')

const listStates = ref([])
const listCities = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])
const listGenders = ref([])
const listCircuits = ref([])
const listCommunityCouncils = ref([])
const listCountries = ref([])

const isRequestOngoing = ref(true)
const isMobile = /Mobi/i.test(navigator.userAgent);
const activeTab = ref(null)

const userDetail = ref(null)
const documentDetail = ref(null)
const locationDetail = ref(null)

const advisor = ref({
  type: '',
  message: '',
  show: false
})

const tabsData = [
  {
    icon: 'mdi-cog',
    title: isMobile ? '' : 'General',
  },
  {
    icon: 'mdi-account-credit-card-outline',
    title: isMobile ? '' : 'Documentos',
  },
  {
    icon: 'mdi-map-marker-radius',
    title: isMobile ? '' : 'Ubicación',
  },
  {
    icon: 'mdi-account-question',
    title: isMobile ? '' : 'Información del Migrante',
  }
]

const loadData = () => {
  listStates.value = miscellaneousStores.getData.states
  listCities.value = miscellaneousStores.getData.cities
  listMunicipalities.value = miscellaneousStores.getData.municipalities
  listParishes.value = miscellaneousStores.getData.parishes
  listGenders.value = miscellaneousStores.getData.genders
  listCircuits.value = miscellaneousStores.getData.circuits
  listCommunityCouncils.value = miscellaneousStores.getData.communityCouncils
  listCountries.value = miscellaneousStores.getData.countries
}

watchEffect(fetchData)

// 👉 Fetch usuarios
async function fetchData() {
  isRequestOngoing.value = true

  const userData = localStorage.getItem('user_data')
    
  userDataJ.value = JSON.parse(userData)
  rol.value = userDataJ.value.roles[0].name

  if(listCommunityCouncils.value.length === 0) {
    await miscellaneousStores.fetchDataMigrant();
    loadData()
  }

  isRequestOngoing.value = false
}


const uploadGeneral = async (data) => {
  userDetail.value = data
  activeTab.value++
}

const uploadDocument = async (data) => {
  documentDetail.value = data
  activeTab.value++
}

const uploadLocation = async (data) => {
  locationDetail.value = data
  activeTab.value++
}

const uploadInfo = async (infoDetail) => {

  isRequestOngoing.value = true

  let formData = new FormData()

  formData.append('name', userDetail.value.name)
  formData.append('last_name', userDetail.value.last_name)
  formData.append('email', userDetail.value.email)
  formData.append('phone', userDetail.value.phone)
  formData.append('gender_id', userDetail.value.gender_id)
  formData.append('birthdate', userDetail.value.birthdate)
  formData.append('passport_number', documentDetail.value.passport_number)
  formData.append('file_document', documentDetail.value.file_document[0])
  formData.append('parish_id', locationDetail.value.parish_id)
  formData.append('community_council_id', locationDetail.value.community_council_id ?? 0)
  formData.append('address', locationDetail.value.address)
  formData.append('country_id', infoDetail.country_id)
  formData.append('transient', Number(infoDetail.transient))
  formData.append('resident', Number(infoDetail.resident))
  formData.append('process_saime', Number(infoDetail.process_saime))
  formData.append('years_in_country', infoDetail.years_in_country)
  formData.append('antecedents', Number(infoDetail.antecedents))
  formData.append('isMarried', Number(infoDetail.isMarried))
  formData.append('has_children', Number(infoDetail.has_children))
  formData.append('passport_status', Number(infoDetail.passport_status))
  formData.append('children_number', Number(infoDetail.has_children) ? infoDetail.children_number : 0)

  migrantsStores.addMigrant(formData)
    .then((res) => {
      if (res.data.success) {

          let data = {
            message: 'Migrante Creado!',
            error: false
          }

          if(rol.value === 'Operador'){
            advisor.value.show = true
            advisor.value.type = 'success'
            advisor.value.message = 'Migrante Creado!'
            isRequestOngoing.value = false

            setTimeout(() => { 
              window.location.reload();
            }, 3000)
          } else {
            router.push({ name : 'dashboard-admin-migrants'})
            emitter.emit('toast', data)
          }
      } else {

        let data = {
          message: 'ERROR',
          error: true
        }

        if(rol.value === 'Operador'){
          advisor.value.show = true
          advisor.value.type = 'error'
          advisor.value.message = 'ERROR'
          isRequestOngoing.value = false
          
          setTimeout(() => { 
            window.location.reload();
          }, 3000)
        } else {
          router.push({ name : 'dashboard-admin-migrants'})
          emitter.emit('toast', data)
        }
      }
  })
  .catch((err) => {
      let data = {
        message: err,
        error: true
      }

      if(rol.value === 'Operador'){
        advisor.value.show = true
        advisor.value.type = 'error'
        advisor.value.message = err
        isRequestOngoing.value = false
        
        setTimeout(() => { 
          window.location.reload();
        }, 3000)
      } else {
        router.push({ name : 'dashboard-admin-migrants'})
        emitter.emit('toast', data)
      }
  })

}

const back = async () => {
  activeTab.value--
}

</script>

<template>
  <VRow>
    <v-col cols="12">
      <v-alert
        v-if="advisor.show"
        :type="advisor.type"
        class="mb-6">
        {{ advisor.message }}
      </v-alert>
    </v-col>
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
      md="4"
    >
      <h6 class="text-h6 mb-4">
        AGREGAR MIGRANTE
      </h6>

      <VTabs
        v-model="activeTab"
        :direction="isMobile ? 'horizontal' : 'vertical'"
        class="v-tabs-pill disable-tab-transition"
        :stacked="isMobile ? true : false"
        disabled
      >
        <VTab
          v-for="(tabItem, index) in tabsData"
          :key="index"
          :prepend-icon="tabItem.icon"
        >
          {{ tabItem.title }}
        </VTab>
      </VTabs>
    </VCol>

    <VCol
      cols="12"
      md="8"
    >
      <VWindow
        v-if="listCircuits.length > 0"
        v-model="activeTab"
        class="disable-tab-transition"
        :touch="false"
      >
        <VWindowItem>
          <SettingsGeneral 
            :genders="listGenders"
            @submit="uploadGeneral"
          />
        </VWindowItem>

        <VWindowItem>
          <SettingsDocument 
            @back="back"
            @submit="uploadDocument"
          />
        </VWindowItem>

        <VWindowItem>
          <SettingsLocation
            :states="listStates"
            :municipalities="listMunicipalities"
            :parishes="listParishes"
            :circuits="listCircuits"
            :communityCouncils="listCommunityCouncils"
            @back="back"
            @submit="uploadLocation"/>
        </VWindowItem>

        <VWindowItem>
          <SettingsInfoMigrant
            :countries="listCountries"
            @back="back"
            @submit="uploadInfo"
          />
        </VWindowItem>

      </VWindow>
    </VCol>
  </VRow>
</template>

<style lang="scss">

    .v-btn--stacked.v-btn--size-default {
      padding: 1.5% !important
    }

    .v-btn--disabled {
      opacity: 1 !important;
    }
    
    .my-class {
      padding: 1.25rem;
      border-radius: 0.375rem;
      background-color: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
    }
</style>

<route lang="yaml">
    meta:
      action: crear
      subject: migrantes
  </route>