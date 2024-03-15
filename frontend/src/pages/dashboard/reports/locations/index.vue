<script setup>

import router from '@/router'
import Toaster from "@/components/common/Toaster.vue";
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useReportsStores } from '@/stores/useReports'
import { useStatesStores } from '@/stores/useStates'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'

const reportsStores = useReportsStores()
const statesStores = useStatesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()

const migrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalMigrants = ref(0)
const isRequestOngoing = ref(true)
const selectedMigrant = ref({})

const state_id = ref(null)
const stateOld_id = ref(null)
const municipality_id = ref(null)
const municipalityOld_id = ref(null)
const parish_id = ref(null)
const parishOld_id = ref(null)
const listStates = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])

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
    state_id: stateOld_id.value,
    municipality_id: municipalityOld_id.value,
    parish_id: parishOld_id.value
  }

  isRequestOngoing.value = true

  await reportsStores.fetchMigrants(data)
  
  migrants.value = reportsStores.getMigrants
  totalPages.value = reportsStores.last_page
  totalMigrants.value = reportsStores.migrantsTotalCount

  if(listParishes.value.length === 0) {
    await statesStores.fetchStates();
    await municipalitiesStores.fetchMunicipalities();
    await parishesStores.fetchParishes();

    loadStates()
    loadMunicipalities()
    loadParishes()
  }

  isRequestOngoing.value = false
}

const seeMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
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
    municipality_id.value = ''
    state_id.value = _state.name
    stateOld_id.value = _state.id

    listMunicipalitiesByStates.value = listMunicipalities.value.filter(item => item.state_id === _state.id)
  }
}

const selectMunicipalities = municipality => {
  if (municipality) {
    let _municipality = listMunicipalities.value.find(item => item.id === municipality)
    municipality_id.value = _municipality.name
    municipalityOld_id.value = _municipality.id
    parish_id.value = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
    parishOld_id.value = _parish.id

  }
}

const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    state_id: stateOld_id.value,
    municipality_id: municipalityOld_id.value,
    parish_id: parishOld_id.value,
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
                          v-model="state_id"
                          label="Estados"
                          :items="listStates"
                          item-title="name"
                          item-value="name"
                          :menu-props="{ maxHeight: '200px' }"
                          @update:model-value="selectState"
                          clearable
                        />
                    </VCol>
                    <VCol
                        cols="12"
                        sm="4"
                    >
                      <VAutocomplete
                        v-model="municipality_id"
                        label="Municipios"
                        :items="getMunicipalities"
                        :menu-props="{ maxHeight: '200px' }"
                        @update:model-value="selectMunicipalities"
                        clearable
                      />
                    </VCol>

                    <VCol
                        cols="12"
                        sm="4"
                    >
                      <VAutocomplete
                        v-model="parish_id"
                        label="Parroquias"
                        :items="getParishes"
                        :menu-props="{ maxHeight: '200px' }"
                        @update:model-value="selectParishes"
                        clearable
                      />
                    </VCol>
                    <VCol cols="12" sm="4" ></VCol>
                    <VCol cols="12" sm="4" ></VCol>
                    <VCol cols="12" sm="4" >
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
                <th scope="col"> ESTADO </th>
                <th scope="col"> UBICACIÓN </th>
                <th scope="col"> CIRCUITO </th>
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
                <td class="text-uppercase"> {{ migrant.parish.municipality.state.name }} </td>
                <td class="text-uppercase">
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base font-weight-medium mb-0">
                        {{ migrant.parish.municipality.name }}
                      </h6>
                      <span class="text-disabled text-sm">{{ migrant.parish.name }}</span>
                    </div>
                  </div>
                </td>
                <td class="text-wrap"> 
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base font-weight-medium mb-0 text-primary"  @click="seeCircuit(migrant.community_council?.circuit)">
                        {{ migrant.community_council?.circuit.name }}
                      </h6>
                      <span class="text-disabled text-sm"></span>
                    </div>
                  </div>
                </td>
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