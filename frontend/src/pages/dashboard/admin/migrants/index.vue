<script setup>

import router from '@/router'
import Toaster from "@/components/common/Toaster.vue";
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useMigrantsStores } from '@/stores/useMigrants'

const migrantsStores = useMigrantsStores()

const migrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalMigrants = ref(0)
const isRequestOngoing = ref(true)
const isConfirmDeleteDialogVisible = ref(false)
const selectedMigrant = ref({})

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
    page: currentPage.value
  }

  isRequestOngoing.value = true

  await migrantsStores.fetchMigrants(data)
  migrants.value = migrantsStores.getMigrants
  totalPages.value = migrantsStores.last_page
  totalMigrants.value = migrantsStores.migrantsTotalCount

  isRequestOngoing.value = false
}

const seeMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
}

const editMigrant = migrantData => {
  router.push({ name : 'dashboard-admin-migrants-edit-id', params: { id: migrantData.id } })
}

const showDeleteDialog = migrantData => {
  isConfirmDeleteDialogVisible.value = true
  selectedMigrant.value = { ...migrantData }
}

const removeMigrant = async () => {
  isConfirmDeleteDialogVisible.value = false
  let res = await migrantsStores.deleteMigrant({ ids: [selectedMigrant.value.id] })
  selectedMigrant.value = {}

  advisor.value = {
    type: res.data.success ? 'success' : 'error',
    message: res.data.success ? 'Migrante eliminado con éxito!' : res.data.message,
    show: true
  }

  await fetchData()

  setTimeout(() => {
    advisor.value = {
      type: '',
      message: '',
      show: false
    }
  }, 3000)

  return true
}

const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    limit: -1
  }

  await migrantsStores.fetchMigrants(data)

  let dataArray = [];
  
  migrantsStores.getMigrants.forEach(element => {

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

        <VCard title="">
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

            <v-spacer />

            <div class="d-flex align-center" style="width: 20rem;">
              <VTextField
                v-model="searchQuery"
                label="Buscar"
                placeholder="Buscar"
                density="compact"
                clearable
                />
            </div>

            <div class="d-flex align-center flex-wrap gap-4">
              <!-- 👉 Add user button -->
              <v-btn
                v-if="$can('crear','migrantes')"
                prepend-icon="tabler-plus"
                :to="{ name: 'dashboard-admin-migrants-add' }">
                  Agregar Migrante
              </v-btn>
            </div>
          </VCardText>

          <v-divider />

          <v-table class="text-no-wrap">
            <!-- 👉 table head -->
            <thead>
              <tr>
                <th scope="col"> #ID </th>
                <th scope="col"> NOMBRE </th>
                <th scope="col"> TELÉFONO </th>
                <th scope="col"> EMAIL </th>
                <th scope="col"> DIRECCIÓN </th>
                <th scope="col" v-if="$can('ver','migrantes') || $can('editar','migrantes') || $can('eliminar','migrantes')">
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
                <td class="text-wrap"> {{migrant.phone }} </td>
                <td class="text-wrap"> {{migrant.email }} </td>
                <td class="text-wrap"> {{migrant.address }} </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('ver','migrantes') || $can('editar','migrantes') || $can('eliminar','migrantes')">      
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

                  <VBtn
                    v-if="$can('editar','migrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="editMigrant(migrant)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-edit" />
                  </VBtn>

                  <VBtn
                    v-if="$can('eliminar','migrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="showDeleteDialog(migrant)">
                              
                    <VIcon
                      size="22"
                      icon="tabler-trash" />
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

    <!-- 👉 Confirm Delete -->
    <VDialog
      v-model="isConfirmDeleteDialogVisible"
      persistent
      class="v-dialog-sm" >
      <!-- Dialog close btn -->
        
      <DialogCloseBtn @click="isConfirmDeleteDialogVisible = !isConfirmDeleteDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Eliminar Migrante">
        <VCardText>
          Está seguro de eliminar el migrante <strong>{{ selectedMigrant.name }} {{ selectedMigrant.last_name }}</strong>?.
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isConfirmDeleteDialogVisible = false">
              Cancelar
          </VBtn>
          <VBtn @click="removeMigrant">
              Aceptar
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
    <!-- MENSAJE COPIADO -->
    <VSnackbar
      v-model="alert.show"
      location="center"
      :timeout="1000"
    >
      {{ alert.message }}
    </VSnackbar>
    <!-- !SECTION -->
  </section>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: migrantes
</route>