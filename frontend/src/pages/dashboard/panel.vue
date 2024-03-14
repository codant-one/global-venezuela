<script setup>

import VueApexCharts from 'vue3-apexcharts'
import Congratulations from "@/components/dashboard/Congratulations.vue";
import Statistics from "@/components/dashboard/Statistics.vue";
import { useStatisticsStores } from '@/stores/useStatistics'

const statisticsStores = useStatisticsStores()


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
const listStatistics = ref([])
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

const timeSpendingChartConfig = {
  chart: {
    height: 157,
    width: 130,
    parentHeightOffset: 0,
    type: 'donut',
  },
  labels: [
    '36h',
    '56h',
    '16h',
    '32h',
    '56h',
    '16h',
  ],
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
  legend: { show: false },
  tooltip: { theme: false },
  grid: { padding: { top: 0 } },
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
            color: 'rgba(var(--v-theme-on-background), var(--v-disabled-opacity))',
            formatter() {
              return '12000'
            },
          },
        },
      },
    },
  },
}

const timeSpendingChartSeries = [
  23,
  35,
  10,
  20,
  35,
  23,
]

const loadStatistics = () => {
  listStatistics.value = statisticsStores.getStatistics
}

watchEffect(fetchData)

async function fetchData() {

    const userData = localStorage.getItem('user_data')
    
    userDataJ.value = JSON.parse(userData)
    name.value = userDataJ.value.name + " " + userDataJ.value.last_name

    await statisticsStores.fetchStatistics();
    loadStatistics()
    total_migrant.value = listStatistics.value.total_migrant
    undocumented.value = listStatistics.value.undocumented
    states.value = listStatistics.value.states
    resident.value = listStatistics.value.resident
    isMarried.value = listStatistics.value.isMarried ?? 0
    has_children.value = listStatistics.value.has_children ?? 0
    transient.value = listStatistics.value.transient ?? 0
    migrant_byuser.value = listStatistics.value.migrant_by_user ?? 0
}

</script>

<template>
  <section>
    <VRow class="py-6 px-md-6 px-2">
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

          <div class="d-flex justify-space-between flex-wrap gap-4 flex-column flex-md-row">
            <div
              v-for="{ title, value, icon, color } in [
                { title: 'Migrantes registrados', value: total_migrant, icon: 'mdi-laptop', color: 'primary' },
                { title: 'Indocumentados', value: undocumented, icon: 'mdi-account-alert-outline', color: 'error' },
                { title: 'Estados reportados', value: states, icon: 'mdi-check-decagram-outline', color: 'success' },
              ]"
              :key="title"
            >
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
      <VCol cols="12" md="4" v-if="false">
        <div class="d-flex justify-space-between align-center">
          <div class="d-flex flex-column ps-3">
            <h5 class="text-h5 text-high-emphasis mb-2 text-no-wrap">
              Registros por origen
            </h5>
            <span class="mb-7">Reporte por país</span>
            <div class="text-h3 mb-2">
              12000
            </div>
            <div>
              <VChip
                color="success"
                label
              >
                +18.4%
              </VChip>
            </div>
          </div>
          <div>
            <VueApexCharts
              type="donut"
              height="150"
              width="150"
              :options="timeSpendingChartConfig"
              :series="timeSpendingChartSeries"
            />
          </div>
        </div>
      </VCol>
    </VRow>

    <VRow class="px-md-6 px-2 match-height">
        <VCol
        cols="12"
        md="7"
        lg="8"
        v-if="resident !== null"
      >
        <Statistics 
          :resident = "resident" 
          :isMarried = "isMarried"
          :has_children = "has_children"
          :transient = "transient"
          class = "h-100" />
      </VCol>

      <!-- <VCol
        cols="12"
        md="5"
        lg="4"
        v-if="migrant_byuser"
      >
        <Congratulations :migrant_byuser="migrant_byuser"/>
      </VCol> -->
    </VRow>

   <!-- <VRow class="px-md-6 px-2">
      <VCol
        cols="12"
        sm="6"
        lg="4"
      >
        <Earning />
      </VCol>

      <VCol
        cols="12"
        sm="6"
        lg="4"
      >
        <Products />
      </VCol>

      <VCol
        cols="12"
        sm="6"
        lg="4"
      >
        <Orders />
      </VCol>
    </VRow>-->

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
