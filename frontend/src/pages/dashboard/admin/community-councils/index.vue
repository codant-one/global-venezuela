<script setup>

import { useCommunityCouncilsStores } from '@/stores/useCommunityCouncils'
import { useStatesStores } from '@/stores/useStates'
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
// import AddNewCommunityCouncilDrawer from './AddNewCommunityCouncilDrawer.vue' 

const communityCouncilsStores = useCommunityCouncilsStores()
const statesStores = useStatesStores()

const communityCouncils = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalCommunityCouncils = ref(0)
const isRequestOngoing = ref(true)
const isAddNewCommunityCouncilDrawerVisible = ref(false)
const isConfirmDeleteDialogVisible = ref(false)
const selectedCommunityCouncil = ref({})
const isDialogVisible = ref(false)
const refForm = ref()
const message = ref('')
const success = ref(false)

const listStates = ref([])

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
  const firstIndex = communityCouncils.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = communityCouncils.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Mostrando ${ firstIndex } hasta ${ lastIndex } de ${ totalCommunityCouncils.value } registros`
})

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPages.value)
    currentPage.value = totalPages.value

  if (!isAddNewCommunityCouncilDrawerVisible.value)
    selectedCommunityCouncil.value = {}
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

  await communityCouncilsStores.fetchCommunityCouncils(data)
  communityCouncils.value = communityCouncilsStores.getCommunityCouncils
  totalPages.value = communityCouncilsStores.last_page
  totalCommunityCouncils.value = communityCouncilsStores.communityCouncilsTotalCount

  isRequestOngoing.value = false
}

const getColor = () =>  {

    const colors = [
        'success',
        'warning',
        'info',
        'primary',
        'secondary',
        'error',
        'secondary'
    ]

    return { color: colors[Math.floor(Math.random() * colors.length)]}
}

// 👉 dialog close
const closeNavigationDrawer = () => {
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()

    isDialogVisible.value = false
    message.value = ''
    success.value = false
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
     
        let data = {
            id: selectedCommunityCouncil.value.id,
        }

        communityCouncilsStores.showCommunityCouncil(data)
            .then((res) => {
                message.value = res.data.message
                success.value = res.data.success
            })
            .catch((err) => {
                advisor.value = {
                    type: 'error',
                    message: err,
                    show: true
                }
            })
    }
  })
}

const submitForm = async (communityCouncil, method) => {
  isRequestOngoing.value = true

  if (method === 'update') {
    communityCouncil.data.append('_method', 'PUT')
    submitUpdate(communityCouncil)
    return
  }

  submitCreate(communityCouncil.data)
}

const submitCreate = communityCouncilData => {

  communityCouncilsStores.addCommunityCouncil(communityCouncilData)
    .then((res) => {
      if (res.data.success) {
        advisor.value = {
          type: 'success',
          message: 'Consejo Comunal creado con éxito',
          show: true
        }
        fetchData()
      }
      isRequestOngoing.value = false
    })
    .catch((err) => {
      advisor.value = {
        type: 'error',
        message: err,
        show: true
      }
      isRequestOngoing.value = false
  })

  setTimeout(() => {
    advisor.value = {
      type: '',
      message: '',
      show: false
    }
  }, 3000)
}

const submitUpdate = communityCouncilData => {

  communityCouncilsStores.updateCommunityCouncil(communityCouncilData)
    .then((res) => {
      if (res.data.success) {
        advisor.value = {
          type: 'success',
          message: 'Consejo Comunal actualizado con éxito',
          show: true
        }
        fetchData()
      }
      isRequestOngoing.value = false
    })
    .catch((err) => {
      advisor.value = {
        type: 'error',
        message: err,
        show: true
      }
      isRequestOngoing.value = false
    })

  setTimeout(() => {
    advisor.value = {
      type: '',
      message: '',
      show: false
    }
  }, 3000)
}


const editCommunityCouncil = communityCouncilData => {
    isAddNewCommunityCouncilDrawerVisible.value = true
    selectedCommunityCouncil.value = { ...communityCouncilData }
}

const showDeleteDialog = communityCouncilData => {
  isConfirmDeleteDialogVisible.value = true
  selectedCommunityCouncil.value = { ...communityCouncilData }
}

const removeCommunityCouncil = async () => {
  isConfirmDeleteDialogVisible.value = false
  let res = await communityCouncilsStores.deleteCommunityCouncil({ ids: [selectedCommunityCouncil.value.id] })
  selectedCommunityCouncil.value = {}

  advisor.value = {
    type: res.data.success ? 'success' : 'error',
    message: res.data.success ? 'Consejo Comunal eliminado con éxito!' : res.data.message,
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

  let data = { limit: -1 }

  await communityCouncilsStores.fetchCommunityCouncils(data)

  let dataArray = [];
  
  communityCouncilsStores.getCommunityCouncils.forEach(element => {
    let data = {
      ID: element.id,
      NOMBRE: element.name,
      ESTADO: element.parish.municipality.state.name,
      MUNICIPIO: element.parish.municipality.name,
      PARROQUIA: element.parish.name
    }

    dataArray.push(data)
  })

  excelParser()
    .exportDataFromJSON(dataArray, "community-councils", "csv");

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

        <v-card title="Filtros">
          <v-card-text class="d-flex flex-wrap py-4 gap-4">
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

            <div class="d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="width: 10rem;">
                <v-text-field
                  v-model="searchQuery"
                  placeholder="Buscar"
                  density="compact"/>
              </div>

              <!-- 👉 Add user button -->
              <v-btn
                v-if="$can('crear','consejos-comunales')"
                prepend-icon="tabler-plus"
                @click="isAddNewCommunityCouncilDrawerVisible = true">
                  Agregar Consejo Comunal
              </v-btn>
            </div>
          </v-card-text>

          <v-divider />

          <v-table class="text-no-wrap">
            <!-- 👉 table head -->
            <thead>
              <tr>
                <th scope="col"> #ID </th>
                <th scope="col"> NOMBRE </th>
                <th scope="col"> ESTADO </th>
                <th scope="col"> UBICACIÓN </th>
                <th scope="col" v-if="$can('editar','consejos-comunales') || $can('eliminar','consejos-comunales')">
                  ACCIONES
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="communityCouncil in communityCouncils"
                :key="communityCouncil.id"
                style="height: 3.75rem;">

                <td> {{communityCouncil.id }} </td>
                <td class="text-base font-weight-medium mb-0"> {{communityCouncil.name }} </td>
                <td class="text-uppercase"> {{communityCouncil.parish.municipality.state.name }} </td>
                <td class="text-wrap"> 
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base font-weight-medium mb-0">
                        {{communityCouncil.parish.municipality.name }}
                      </h6>
                      <span class="text-disabled text-sm">{{communityCouncil.parish.name }}</span>
                    </div>
                  </div>
                </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('editar','consejos-comunales') || $can('eliminar','consejos-comunales')">      
                  <VBtn
                    v-if="$can('editar','consejos-comunales')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="editCommunityCouncil(communityCouncil)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-edit" />
                  </VBtn>

                  <VBtn
                    v-if="$can('eliminar','consejos-comunales')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="showDeleteDialog(communityCouncil)">
                              
                    <VIcon
                      size="22"
                      icon="tabler-trash" />
                  </VBtn>
                </td>
              </tr>
            </tbody>
            <!-- 👉 table footer  -->
            <tfoot v-show="!communityCouncils.length">
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
        </v-card>
      </v-col>
    </v-row>

    <!-- 👉 Add New Community Council -->
    <!-- <AddNewCommunityCouncilDrawer
      v-model:isDrawerOpen="isAddNewCommunityCouncilDrawerVisible"
      :communityCouncil="selectedCommunityCouncil"
      @community-council-data="submitForm"/> -->

    <!-- 👉 Confirm Delete -->
    <VDialog
      v-model="isConfirmDeleteDialogVisible"
      persistent
      class="v-dialog-sm" >
      <!-- Dialog close btn -->
        
      <DialogCloseBtn @click="isConfirmDeleteDialogVisible = !isConfirmDeleteDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Eliminar Consejo Comunal">
        <VCardText>
          Está seguro de eliminar el consejo comunal <strong>{{ selectedCommunityCouncil.name }}</strong>?.
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isConfirmDeleteDialogVisible = false">
              Cancelar
          </VBtn>
          <VBtn @click="removeCommunityCouncil">
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
    subject: consejos-comunales
</route>