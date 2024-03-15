<script setup>

import router from '@/router'
import Toaster from "@/components/common/Toaster.vue";
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useReportsStores } from '@/stores/useReports'
import { useCountriesStores } from '@/stores/useCountries'

const reportsStores = useReportsStores()
const countriesStores = useCountriesStores()

const migrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalMigrants = ref(0)
const isRequestOngoing = ref(true)
const selectedMigrant = ref({})

const listCountries = ref([])
const country_id = ref(null)

const url = ref('https://hatscripts.github.io/circle-flags/flags/')
const ext = ref('.svg')

const advisor = ref({
  type: '',
  message: '',
  show: false
})

const alert = ref({
  show: false,
  message: ''
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = migrants.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = migrants.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Mostrando ${ firstIndex } hasta ${ lastIndex } de ${ totalMigrants.value } registros`
})

const loadCountries = () => {
  listCountries.value = countriesStores.getCountries
}

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPages.value)
    currentPage.value = totalPages.value
})

watchEffect(fetchData)

async function fetchData() {

  let data = {
    search: searchQuery.value,
    orderByField: 'id',
    orderBy: 'desc',
    limit: rowPerPage.value,
    page: currentPage.value,
    country_id: country_id.value
  }

  isRequestOngoing.value = true

  await reportsStores.fetchMigrants(data)
  
  migrants.value = reportsStores.getMigrants
  totalPages.value = reportsStores.last_page
  totalMigrants.value = reportsStores.migrantsTotalCount

  await countriesStores.fetchCountries();
  loadCountries()

  isRequestOngoing.value = false
}

const seeMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
}


const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    country_id: country_id.value,
    limit: -1
  }

  await reportsStores.fetchMigrants(data)

  let dataArray = [];
  
  reportsStores.getMigrants.forEach(element => {

    let data = {
      NOMBRE: element.name,
      APELLIDO: element.last_name,
      EMAIL: element.email,
      FECHA_NACIMIENTO: element.birthdate,
      PAÍS_MIGRANTE: element.country.name,
      GÉNERO: element.gender.name,
      NÚMERO_PASAPORTE: element.passport_number,
      PASAPORTE_VIGENTE: element.passport_status ? 'SI' : 'NO',
      TELÉFONO: element.phone,
      DIRECCIÓN: element.address.replace(/\r?\n/g, " "),
      CÉDULA_TRANSEUNTE: element.transient ? 'SI' : 'NO',
      CÉDULA_RESIDENTE: element.resident ? 'SI' : 'NO',
      AÑOS_EN_EL_PAÍS: element.years_in_country,
      ANTECEDENTES_PENALES: element.antecedents ? 'SI' : 'NO',
      CASADO_CON_UN_VENEZOLANO: element.isMarried ? 'SI' : 'NO',
      HIJOS_VENEZOLANOS: element.has_children ? 'SI' : 'NO',
      NÚMERO_HIJOS_VENEZOLANOS: element.children_number ?? '',
      ESTADO: element.parish.municipality.state.name,
      MUNICIPIO: element.parish.municipality.name,
      PARROQUIA: element.parish.name,
      CONSEJO_COMUNAL: element.community_council?.name
    }

    dataArray.push(data)
  })

  excelParser()
    .exportDataFromJSON(dataArray, "migrants", "csv");

  isRequestOngoing.value = false

}

</script>

<template>
  <section>
    <v-row>
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

      <v-col cols="12">
        <Toaster />
        <v-alert
          v-if="advisor.show"
          :type="advisor.type"
          class="mb-6">
            
          {{ advisor.message }}
        </v-alert>

        <VCard title="Filtros">
            <VCardText>
                <VRow>
                    <VCol
                        cols="12"
                        sm="4"
                    >
                        <VAutocomplete
                            v-model="country_id"
                            label="Paises"
                            :items="listCountries"
                            item-value="id"
                            item-title="name"
                            clearable
                            clear-icon="tabler-x"
                            no-data-text="No disponible"
                        />
                    </VCol>
                <VCol cols="12" sm="2" />
                <VCol
                    cols="12"
                    sm="6"
                >
                    <VTextField
                    v-model="searchQuery"
                    label="Buscar"
                    placeholder="Buscar"
                    density="compact"
                    clearable
                    />
                </VCol>
                </VRow>
            </VCardText>

            <VDivider />

            <VCardText class="d-flex flex-wrap py-4 gap-4">
                <div
                class="me-3"
                style="width: 80px;">
                
                <VSelect
                    v-model="rowPerPage"
                    density="compact"
                    variant="outlined"
                    :items="[10, 20, 30, 50]"/>
                </div>

                <div class="d-flex align-center">
                <VBtn
                    variant="tonal"
                    color="secondary"
                    prepend-icon="tabler-file-export"
                    @click="downloadCSV">
                    Exportar
                </VBtn>
                </div>

            </VCardText>

          <v-divider />

          <v-table class="text-no-wrap">
            <!-- 👉 table head -->
            <thead>
              <tr>
                <th scope="col"> #ID </th>
                <th scope="col"> NOMBRE </th>
                <th scope="col"> PAÍS DE ORIGEN </th>
                <th scope="col"> TELÉFONO </th>
                <th scope="col"> E-MAIL </th>
                <th scope="col" v-if="$can('ver','migrantes')">
                  ACCIONES
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="migrant in migrants"
                :key="migrant.id"
                style="height: 3.75rem;">

                <td> {{migrant.id }} </td>
                <td class="text-wrap text-base font-weight-medium mb-0"> {{migrant.name }}  {{migrant.last_name }} </td>
                <td>
                    <div class="d-flex align-center">
                      <img v-if="migrant.country.flag"
                        class="me-3"
                        :src="url + migrant.country.iso.toLowerCase() + ext" width="35"/>
                      
                      <span v-else>{{ avatarText(migrant.country.name) }}</span>
                      <div class="d-flex flex-column">
                        <span class="text-base">{{ migrant.country.name }}</span>
                      </div>
                    </div>
                  </td>
                <td> {{migrant.phone }} </td>
                <td> {{migrant.email }} </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('ver','migrantes')">      
                  <VBtn
                    v-if="$can('ver','migrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="seeMigrant(migrant)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-eye" />
                  </VBtn>
                </td>
              </tr>
            </tbody>
            <!-- 👉 table footer  -->
            <tfoot v-show="!migrants.length">
              <tr>
                <td
                  colspan="7"
                  class="text-center">
                  Datos no disponibles
                </td>
              </tr>
            </tfoot>
          </v-table>
        
          <v-divider />

          <VCardText class="d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5">
            <span class="text-sm text-disabled">
              {{ paginationData }}
            </span>

            <VPagination
              v-model="currentPage"
              size="small"
              :total-visible="5"
              :length="totalPages"/>
          
          </VCardText>
        </VCard>
      </v-col>
    </v-row>
    <!-- !SECTION -->
  </section>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: reportes
</route>