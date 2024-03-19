<script setup>

import router from '@/router'
import VueApexCharts from 'vue3-apexcharts'
import Congratulations from "@/components/dashboard/Congratulations.vue";
import Countries from "@/components/dashboard/Countries.vue";
import Statistics from "@/components/dashboard/Statistics.vue";
import { useStatisticsStores } from '@/stores/useStatistics'

const statisticsStores = useStatisticsStores()

const isRequestOngoing = ref(true)

const isAdmin = ref(false)
const userDataJ = ref('')
const name = ref('')
const total_migrant = ref('')
const undocumented = ref('')
const states = ref('')
const resident = ref(null)
const isMarried = ref(null)
const has_children = ref(null)
const transient = ref(null)
const migrant_byuser = ref(null)
const listStatistics = ref(null)
const volunteerCount = ref(null)
const userCount = ref(null)
const listCountries = ref([])

const donutChartColors = {
  donut: {
    series1: '#F9A01B',
    series2: '#FAD847',
    series3: '#FCE07D',
    series4: '#FDE7B2',
    series5: '#FEF0E8',
    series6: '#FFF8FD',
  },
}

const headingColor = 'rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity))'
const labelColor = 'rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity))'

const timeSpendingChartConfig = ref({
  labels: [],
  colors: [
    donutChartColors.donut.series1,
    donutChartColors.donut.series2,
    donutChartColors.donut.series3,
    donutChartColors.donut.series4,
    donutChartColors.donut.series5,
    donutChartColors.donut.series6,
  ],
  stroke: { width: 0 },
  dataLabels: {
    enabled: false,
    formatter(val) {
      return `${ Number.parseInt(val) }%`
    },
  },
  legend: {
    show: true,
    position: 'bottom',
    offsetY: 10,
    markers: {
      width: 8,
      height: 8,
      offsetX: -3,
    },
    itemMargin: {
      horizontal: 15,
      vertical: 5,
    },
    fontSize: '13px',
    fontWeight: 400,
    labels: {
      colors: headingColor,
      useSeriesColors: false,
    },
  },
  tooltip: { theme: false },
  grid: { padding: { top: 15 } },
  plotOptions: {
    pie: {
      donut: {
        size: '75%',
        labels: {
          show: true,
          value: {
            fontSize: '1.5rem',
            color: 'rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity))',
            fontWeight: 500,
            offsetY: -15,
            formatter(val) {
              return `${ Number.parseInt(val) }%`
            },
          },
          name: { offsetY: 20 },
          total: {
            show: true,
            fontSize: '.7rem',
            label: 'Total',
            color: labelColor,
            formatter() {
              return total_migrant.value
            },
          },
        },
      },
    },
  },
  responsive: [{
    breakpoint: 420,
    options: { 
      chart: { 
        height: 270, 
        width: 270
      }
    },
  }]
})

const timeSpendingChartSeries = ref([])

const loadStatistics = () => {
  listStatistics.value = statisticsStores.getStatistics
}

const volunteerData = ref([])

watchEffect(fetchData)

async function fetchData() {

  isRequestOngoing.value = true
    
  const userData = localStorage.getItem('user_data')
    
  userDataJ.value = JSON.parse(userData)
  name.value = userDataJ.value.name + " " + userDataJ.value.last_name

  isAdmin.value = (userDataJ.value.roles[0].name !== 'Operador')

  await statisticsStores.fetchStatistics();
  loadStatistics()
    
  total_migrant.value = listStatistics.value.total_migrant
  undocumented.value = listStatistics.value.undocumented
  states.value = listStatistics.value.states + '/' + listStatistics.value.stateCount
  volunteerCount.value = listStatistics.value.volunteerCount
  userCount.value = listStatistics.value.userCount
  resident.value = listStatistics.value.resident
  isMarried.value = listStatistics.value.isMarried ?? 0
  has_children.value = listStatistics.value.has_children ?? 0
  transient.value = listStatistics.value.transient ?? 0
  migrant_byuser.value = listStatistics.value.migrant_by_user ?? 0

  listCountries.value = listStatistics.value.countries

  listCountries.value.forEach( function  (element) { 
    timeSpendingChartConfig.value.labels.push(element.name)
    timeSpendingChartSeries.value.push(element.total)
  });

  timeSpendingChartConfig.value.plotOptions.pie.donut.labels.value.formatter = (val, opts) => {
    const element = listCountries.value.filter(item => item.total === Number.parseInt(val))[0]
    return (typeof element === 'undefined') ? '' : `${element.percentage}%`;
  };

  volunteerData.value = [
    {
      title: 'Estados',
      registers: listStatistics.value.stateCount,
      completed: listStatistics.value.completedState,
      total: listStatistics.value.completedState + '/' + listStatistics.value.stateCount,
      progress: ((listStatistics.value.completedState * 100) / listStatistics.value.stateCount).toFixed(2),
      color: 'primary',
      link: 'dashboard-volunteers-states'
    },
    {
      title: 'Municipios',
      registers: listStatistics.value.municipalityCount,
      completed: listStatistics.value.completedMunicipality,
      total: listStatistics.value.completedMunicipality + '/' + listStatistics.value.municipalityCount,
      progress: ((listStatistics.value.completedMunicipality * 100) / listStatistics.value.municipalityCount).toFixed(2),
      color: 'success',
      link: 'dashboard-volunteers-municipalities'
    },
    {
      title: 'Circuitos',
      registers: listStatistics.value.circuitCount,
      completed: listStatistics.value.completedCircuit,
      total: listStatistics.value.completedCircuit + '/' + listStatistics.value.circuitCount,
      progress: ((listStatistics.value.completedCircuit * 100) / listStatistics.value.circuitCount).toFixed(2),
      color: 'error',
      link: 'dashboard-volunteers-circuits'
    }
  ]

  isRequestOngoing.value = false
}

const go = (name) => {
  router.push({ name : name})
}

</script>

<template>
  <section>
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

    <VRow class="py-6 px-md-6 px-2" v-if="listStatistics">
      <VCol
        cols="12"
        md="8"
        :class="$vuetify.display.mdAndUp ? 'border-e' : 'border-b'"
      >
        <div class="pe-3">
          <h3 class="text-h3 text-high-emphasis mb-1">
            Bienvenido de nuevo, <span class="font-weight-medium"> {{ name }} 👋🏻 </span>
          </h3>

          <div
            class="mb-7 text-wrap"
            style="max-inline-size: 800px;"
          >
            En este panel encontraras información relevante sobre el registro de migrantes.
          </div>

          <div 
            v-if="listStatistics"
            class="d-flex justify-space-between flex-wrap gap-4 flex-column flex-md-row">
            <div
              v-for="{ title, value, icon, color } in [
                { title: 'Migrantes registrados', value: total_migrant, icon: 'mdi-laptop', color: 'primary' },
                { title: 'Indocumentados', value: undocumented, icon: 'mdi-account-alert-outline', color: 'error' },
                { title: 'Estados reportados', value: states, icon: 'mdi-check-decagram-outline', color: 'success' },
              ]"
              :key="title">
              <div class="d-flex">
                <VAvatar
                  variant="tonal"
                  :color="color"
                  rounded
                  size="54"
                  class="text-primary me-4"
                >
                  <VIcon
                    :icon="icon"
                    size="38"
                  />
                </VAvatar>
                <div>
                  <span class="text-base">{{ title }}</span>
                  <h4
                    class="text-h4 font-weight-medium"
                    :class="`text-${color}`"
                  >
                    {{ value }}
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </VCol>
      <VCol
        cols="12"
        md="5"
        lg="4"
        v-if="migrant_byuser !== null"
      >
        <Congratulations :migrant_byuser="migrant_byuser"/>
      </VCol>
    </VRow>

    <VRow class="px-md-6 px-2 match-height" v-if="listStatistics">
      <VCol cols="12" md="8" lg="8" v-if="resident !== null">
        <Statistics 
          :resident = "resident" 
          :isMarried = "isMarried"
          :has_children = "has_children"
          :transient = "transient"
          class = "h-100" />
      </VCol>
      <VCol cols="12" md="2" lg="2" v-if="isAdmin">
        <VCard class="cursor-pointer" @click="go('dashboard-volunteers-states')"> 
          <VCardText class="d-flex flex-column align-center justify-center">
            <VAvatar
              size="50"
              variant="tonal"
              color="primary"
            >
              <VIcon icon="mdi-account-tie" size="x-large"/>
            </VAvatar>

            <h5 class="text-h5 my-2">
             {{ volunteerCount }}
            </h5>
            <span class="text-body-2">Voluntarios</span>
          </VCardText>
        </VCard>
      </VCol>
      <VCol cols="12" md="2" lg="2" v-if="isAdmin">
        <VCard class="cursor-pointer" @click="go('dashboard-admin-users')">
          <VCardText class="d-flex flex-column align-center justify-center">
            <VAvatar
              size="50"
              variant="tonal"
              color="info"
            >
              <VIcon icon="mdi-account-star-outline" size="x-large"/>
            </VAvatar>

            <h5 class="text-h5 my-2">
             {{ userCount }}
            </h5>
            <span class="text-body-2">Mis Usuarios</span>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

   <VRow class="px-md-6 px-2" v-if="listStatistics && isAdmin">
      <VCol cols="12" sm="6" lg="4">
        <Countries 
          v-if="listCountries.length > 0"
          :countries="listCountries"/>
      </VCol>

      <VCol cols="12" md="4">
        <VCard>
          <VCardItem title="Registro de Migrantes por país" />
          <VCardText>
            <VueApexCharts
              v-if="timeSpendingChartConfig.labels.length > 0"
              type="donut"
              height="300"
              width="300"
              :options="timeSpendingChartConfig"
              :series="timeSpendingChartSeries"
            />
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12" md="4">
        <VCard>
        <VCardItem title="Voluntareado"/>
        <VCardText>
          <VList class="card-list">
            <VListItem
              v-for="volunteer in volunteerData"
              :key="volunteer.title"
            >
              <template #prepend>
                <VProgressCircular
                  v-model="volunteer.progress"
                  :size="54"
                  class="me-4"
                  :color="volunteer.color"
                >
                  <span class="text-body-2 text-high-emphasis font-weight-medium">
                    {{ volunteer.progress }}%
                  </span>
                </VProgressCircular>
              </template>
              <VListItemTitle class="font-weight-medium mb-2">
                {{ volunteer.title }} {{ volunteer.total }}
              </VListItemTitle>

              <VListItemSubtitle>{{ volunteer.completed }} Registros completados</VListItemSubtitle>
              <template #append>
                <VBtn
                  variant="tonal"
                  color="default"
                  class="rounded-sm"
                  size="30"
                  @click="go(volunteer.link)"
                >
                  <VIcon
                    icon="tabler-chevron-right"
                    class="flip-in-rtl"
                  />
                </VBtn>
              </template>
            </VListItem>
          </VList>
        </VCardText>
      </VCard>
      </VCol>
    </VRow>

  </section>
</template>

<style lang="scss" scoped>
  .card-list {
    --v-card-list-gap: 30px;
  }
</style>

<route lang="yaml">
  meta:
    action: ver
    subject: dashboard
</route>
