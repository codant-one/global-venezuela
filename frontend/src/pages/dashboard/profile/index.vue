<script setup>

import { useStatesStores } from '@/stores/useStates'
import { useCitiesStores } from '@/stores/useCities'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'
import { useGendersStores } from '@/stores/useGenders'
import TabSecurity from '@/views/dashboard/profile/TabSecurity.vue'
import UserProfile from '@/views/dashboard/profile/UserProfile.vue'

const statesStores = useStatesStores()
const citiesStores = useCitiesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()
const gendersStores = useGendersStores()

const avatar = ref('')
const avatarOld = ref('')
const userData = ref(null)
const userTab = ref(null)

const listStates = ref([])
const listCities = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])
const listGenders = ref([])

const tabs = [
  {
    icon: 'tabler-lock',
    title: 'Seguridad',
  }
]

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


watchEffect(fetchData)

async function fetchData() { 

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

  userData.value = JSON.parse(localStorage.getItem('user_data') || 'null')

  avatarOld.value = userData.value.avatar
  avatar.value = userData.value.avatar
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
</script>

<template>
  <section>
    <VRow v-if="userData">
      <VCol
        cols="12"
        md="5"
        lg="4"
      >
        <UserProfile
          :user="userData"
          :states="listStates"
          :cities="listCities"
          :municipalities="listMunicipalities"
          :parishes="listParishes"
          :genders="listGenders"
          :avatarOld="avatarOld"
          :avatar="avatar"
          @onImageSelected="onImageSelected" />
      </VCol>
      <VCol
        cols="12"
        md="7"
        lg="8"
      >
        <VTabs
          v-model="userTab"
          class="v-tabs-pill"
        >
          <VTab
            v-for="tab in tabs"
            :key="tab.icon"
          >
            <VIcon
              :size="18"
              :icon="tab.icon"
              class="me-1"
            />
            <span>{{ tab.title }}</span>
          </VTab>
        </VTabs>

        <VWindow
          v-model="userTab"
          class="mt-6 disable-tab-transition"
          :touch="false"
        >
          <VWindowItem>
            <TabSecurity />
          </VWindowItem>
        </VWindow>
      </VCol>
    </VRow>
  </section>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: dashboard
</route>
