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
    woman: null,
    man: null,
    isMarried: null,
    has_children: null
})

const advisor = ref({
  type: '',
  message: '',
  show: false
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
    woman: infoDetail.value.woman === null ? null : Number(infoDetail.value.woman),
    man: infoDetail.value.man === null ? null : Number(infoDetail.value.man),
    isMarried: infoDetail.value.isMarried === null ? null : Number(infoDetail.value.isMarried),
    has_children: infoDetail.value.has_children === null ? null : Number(infoDetail.value.has_children)
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
    woman: null,
    man: null,
    isMarried: null,
    has_children: null
  }
}

const seeMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
}


const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    woman: infoDetail.value.woman === null ? null : Number(infoDetail.value.woman),
    man: infoDetail.value.man === null ? null : Number(infoDetail.value.man),
    isMarried: infoDetail.value.isMarried === null ? null : Number(infoDetail.value.isMarried),
    has_children: infoDetail.value.has_children === null ? null : Number(infoDetail.value.has_children),
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
                        <span class="me-2">Mujer</span>
                        <VCheckbox
                          v-if="infoDetail.woman !== null"
                          v-model="infoDetail.woman"
                          :label="infoDetail.woman === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.woman"
                        />
                      </div>
                    </VCol>
                    <VCol
                        cols="12"
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Hombre</span>
                        <VCheckbox
                          v-if="infoDetail.man !== null"
                          v-model="infoDetail.man"
                          :label="infoDetail.man === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.man"
                        />
                      </div>
                    </VCol>
                    <VCol
                        cols="12"
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Casado</span>
                        <VCheckbox
                          v-if="infoDetail.isMarried !== null"
                          v-model="infoDetail.isMarried"
                          :label="infoDetail.isMarried === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.isMarried"
                        />
                      </div>
                    </VCol>

                    <VCol
                        cols="12"
                        sm="2"
                    >
                      <div class="align-center d-flex">
                        <span class="me-2">Con hijos</span>
                        <VCheckbox
                          v-if="infoDetail.has_children !== null"
                          v-model="infoDetail.has_children"
                          :label="infoDetail.has_children === true ? 'SI' : 'NO'"
                          true-icon="tabler-check"
                          false-icon="tabler-x"
                        />
                        <VCheckbox
                          v-else
                          v-model="infoDetail.has_children"
                        />
                      </div>
                    </VCol>
                    <VCol cols="12" sm="3"></VCol>
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
                <th scope="col"> GÉNERO</th>
                <th scope="col"> CASADO </th>
                <th scope="col"> CON HIJOS</th>
                <th scope="col"> CUANTOS</th>
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
                    {{ migrant.gender.name }}
                </td>
                <td>
                  <VChip v-if="migrant.isMarried" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
                <td>
                  <VChip v-if="migrant.has_children" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
                <td>
                  <span v-if="migrant.has_children" class="text-primary">{{ migrant.children_number ?? 1 }}</span>
                  <span v-else class="text-error">0</span>
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