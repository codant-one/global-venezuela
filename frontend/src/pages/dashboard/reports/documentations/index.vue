<script setup>

import router from '@/router'
import Toaster from "@/components/common/Toaster.vue";
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useReportsStores } from '@/stores/useReports'

const reportsStores = useReportsStores()

const migrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalMigrants = ref(0)
const isRequestOngoing = ref(true)

const infoDetail = ref({
    transient: null,
    resident: null,
    process_saime: null,
    antecedents: null,
    isMarried: null,
    has_children: null,
    passport_status: null
})

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
    transient: infoDetail.value.transient === null ? null : Number(infoDetail.value.transient),
    resident: infoDetail.value.resident === null ? null : Number(infoDetail.value.resident),
    process_saime: infoDetail.value.process_saime === null ? null : Number(infoDetail.value.process_saime),
    antecedents: infoDetail.value.antecedents === null ? null : Number(infoDetail.value.antecedents),
    isMarried: infoDetail.value.isMarried === null ? null : Number(infoDetail.value.isMarried),
    has_children: infoDetail.value.has_children === null ? null : Number(infoDetail.value.has_children),
    passport_status: infoDetail.value.passport_status === null ? null : Number(infoDetail.value.passport_status)
  }

  isRequestOngoing.value = true

  await reportsStores.fetchMigrants(data)
  
  migrants.value = reportsStores.getMigrants
  totalPages.value = reportsStores.last_page
  totalMigrants.value = reportsStores.migrantsTotalCount

  isRequestOngoing.value = false
}

const refresh = () => {
  infoDetail.value = { 
    transient: null,
    resident: null,
    process_saime: null,
    antecedents: null,
    isMarried: null,
    has_children: null,
    passport_status: null
  }
}

const seeMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
}


const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    transient: infoDetail.value.transient === null ? null : Number(infoDetail.value.transient),
    resident: infoDetail.value.resident === null ? null : Number(infoDetail.value.resident),
    process_saime: infoDetail.value.process_saime === null ? null : Number(infoDetail.value.process_saime),
    antecedents: infoDetail.value.antecedents === null ? null : Number(infoDetail.value.antecedents),
    isMarried: infoDetail.value.isMarried === null ? null : Number(infoDetail.value.isMarried),
    has_children: infoDetail.value.has_children === null ? null : Number(infoDetail.value.has_children),
    passport_status: infoDetail.value.passport_status === null ? null : Number(infoDetail.value.passport_status),
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
      DIRECCIÓN: (element.address !== null) ? element.address.replace(/\r?\n/g, " ") : '',
      CÉDULA_TRANSEUNTE: element.transient ? 'SI' : 'NO',
      CÉDULA_RESIDENTE: element.resident ? 'SI' : 'NO',
      PROCESO_SAIME: element.process_saime ? 'SI' : 'NO',
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
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Antecendentes</span>
                        <VCheckbox
                          v-if="infoDetail.antecedents !== null"
                          v-model="infoDetail.antecedents"
                          :label="infoDetail.antecedents === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.antecedents"
                        />
                      </div>
                    </VCol>
                    <VCol
                        cols="12"
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Visa transeunte</span>
                        <VCheckbox
                          v-if="infoDetail.transient !== null"
                          v-model="infoDetail.transient"
                          :label="infoDetail.transient === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.transient"
                        />
                      </div>
                    </VCol>
                    <VCol
                        cols="12"
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Céd. residente</span>
                        <VCheckbox
                          v-if="infoDetail.resident !== null"
                          v-model="infoDetail.resident"
                          :label="infoDetail.resident === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.resident"
                        />
                      </div>
                    </VCol>

                    <VCol
                        cols="12"
                        sm="3"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Naturalización ante el SAIME</span>
                        <VCheckbox
                          v-if="infoDetail.process_saime !== null"
                          v-model="infoDetail.process_saime"
                          :label="infoDetail.process_saime === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.process_saime"
                        />
                      </div>
                    </VCol>
                    <VCol cols="12" sm="2">
                      <div class="align-center d-flex">
                        <span class="me-2">Pasaporte vencido</span>
                        <VCheckbox
                          v-if="infoDetail.passport_status !== null"
                          v-model="infoDetail.passport_status"
                          :label="infoDetail.passport_status === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.passport_status"                        />
                      </div>
                    </VCol>
                    <VCol cols="12" sm="1">
                      <div class="align-center d-flex">
                        <VTooltip
                          location="top"
                        >
                          <template #activator="{ props }">
                            <VBtn color="error"  v-bind="props" @click="refresh">
                              <VIcon icon="tabler-trash" />
                            </VBtn>
                          </template>
                          <span>Limpiar</span>
                        </VTooltip>
                      </div>
                    </VCol>
                    <VCol
                        cols="12"
                        sm="4"
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
                <th scope="col"> ANTEDEDENTES</th>
                <th scope="col"> CÉDULA DE TRANSEÚNTE </th>
                <th scope="col"> CÉDULA DE RESIDENTE</th>
                <th scope="col"> PASAPORTE VENCIDO</th>
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

                <td> {{ migrant.id }} </td>
                <td class="text-base font-weight-medium mb-0"> {{migrant.name }}  {{migrant.last_name }} </td>
                <td> 
                  <VChip v-if="migrant.antecedents" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
                <td>
                  <VChip v-if="migrant.transient" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
                <td>
                  <VChip v-if="migrant.resident" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
                <td>
                  <VChip v-if="migrant.passport_status" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
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
<style scoped>
  .radio-custom {
    margin-right: 56px;
  }

  .radio-custom2 {
    margin-right: 150px;
  }
</style>
<route lang="yaml">
  meta:
    action: ver
    subject: reportes
</route>