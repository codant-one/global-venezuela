<script setup>

import router from '@/router'
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useInmigrantsStores } from '@/stores/useInmigrants'

const inmigrantsStores = useInmigrantsStores()

const inmigrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalInmigrants = ref(0)
const isRequestOngoing = ref(true)
const isAddNewInmigrantDrawerVisible = ref(false)
const isConfirmDeleteDialogVisible = ref(false)
const selectedInmigrant = ref({})

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
  const firstIndex = inmigrants.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = inmigrants.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Mostrando ${ firstIndex } hasta ${ lastIndex } de ${ totalInmigrants.value } registros`
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

  await inmigrantsStores.fetchInmigrants(data)
  inmigrants.value = inmigrantsStores.getInmigrants
  totalPages.value = inmigrantsStores.last_page
  totalInmigrants.value = inmigrantsStores.inmigrantsTotalCount

  isRequestOngoing.value = false
}

const seeInmigrant = inmigrantData => {
  router.push({ name : 'dashboard-admin-inmigrants-id', params: { id: inmigrantData.id } })
}

const editInmigrant = inmigrantData => {
    isAddNewInmigrantDrawerVisible.value = true
    selectedInmigrant.value = { ...inmigrantData }
}

const showDeleteDialog = inmigrantData => {
  isConfirmDeleteDialogVisible.value = true
  selectedInmigrant.value = { ...inmigrantData }
}

const removeInmigrant = async () => {
  isConfirmDeleteDialogVisible.value = false
  let res = await inmigrantsStores.deleteInmigrant(selectedInmigrant.value.id)
  selectedInmigrant.value = {}

  advisor.value = {
    type: res.data.success ? 'success' : 'error',
    message: res.data.success ? 'Inmigrante eliminado con éxito!' : res.data.message,
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

  await inmigrantsStores.fetchInmigrants(data)

  let dataArray = [];
  
  inmigrantsStores.getInmigrants.forEach(element => {

    let data = {
      NOMBRE: element.name,
      APELLIDO: element.last_name,
      EMAIL: element.email,
      FECHA_NACIMIENTO: element.birthdate,
      PAÍS_INMIGRANTE: element.country.name,
      GÉNERO: element.gender.name,
      NÚMERO_PASAPORTE: element.passport_number,
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
    .exportDataFromJSON(dataArray, "inmigrants", "csv");

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
                v-if="$can('crear','inmigrantes')"
                prepend-icon="tabler-plus"
                @click="isAddNewInmigrantDrawerVisible = true">
                  Agregar Inmigrante
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
                <th scope="col" v-if="$can('ver','inmigrantes') || $can('editar','inmigrantes') || $can('eliminar','inmigrantes')">
                  ACCIONES
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="inmigrant in inmigrants"
                :key="inmigrant.id"
                style="height: 3.75rem;">

                <td> {{inmigrant.id }} </td>
                <td class="text-base font-weight-medium mb-0"> {{inmigrant.name }} </td>
                <td> {{inmigrant.phone }} </td>
                <td> {{inmigrant.email }} </td>
                <td> {{inmigrant.address }} </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('ver','inmigrantes') || $can('editar','inmigrantes') || $can('eliminar','inmigrantes')">      
                  <VBtn
                    v-if="$can('ver','inmigrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="seeInmigrant(inmigrant)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-eye" />
                  </VBtn>

                  <VBtn
                    v-if="$can('editar','inmigrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="editInmigrant(inmigrant)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-edit" />
                  </VBtn>

                  <VBtn
                    v-if="$can('eliminar','inmigrantes')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="showDeleteDialog(inmigrant)">
                              
                    <VIcon
                      size="22"
                      icon="tabler-trash" />
                  </VBtn>
                </td>
              </tr>
            </tbody>
            <!-- 👉 table footer  -->
            <tfoot v-show="!inmigrants.length">
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
      <VCard title="Eliminar Inmigrante">
        <VCardText>
          Está seguro de eliminar el inmigrante <strong>{{ selectedInmigrant.name }}</strong>?.
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isConfirmDeleteDialogVisible = false">
              Cancelar
          </VBtn>
          <VBtn @click="removeInmigrant">
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
    subject: inmigrantes
</route>