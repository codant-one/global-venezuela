<script setup>

import AddNewCircuitDrawer from './AddNewCircuitDrawer.vue' 
import router from '@/router'
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useCircuitsStores } from '@/stores/useCircuits'
import { useStatesStores } from '@/stores/useStates'
import { useCitiesStores } from '@/stores/useCities'
import { useMunicipalitiesStores } from '@/stores/useMunicipalities'
import { useParishesStores } from '@/stores/useParishes'

const circuitsStores = useCircuitsStores()
const statesStores = useStatesStores()
const citiesStores = useCitiesStores()
const municipalitiesStores = useMunicipalitiesStores()
const parishesStores = useParishesStores()

const circuits = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalCircuits = ref(0)
const isRequestOngoing = ref(true)
const isAddNewCircuitDrawerVisible = ref(false)
const isConfirmDeleteDialogVisible = ref(false)
const selectedCircuit = ref({})
const isDialogVisible = ref(false)
const refForm = ref()
const message = ref('')
const success = ref(false)

const state_id = ref(null)
const listStates = ref([])
const listCities = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])

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
  const firstIndex = circuits.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = circuits.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Mostrando ${ firstIndex } hasta ${ lastIndex } de ${ totalCircuits.value } registros`
})

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPages.value)
    currentPage.value = totalPages.value

  if (!isAddNewCircuitDrawerVisible.value)
    selectedCircuit.value = {}
})

watchEffect(fetchData)

async function fetchData() {
  let data = {
    search: searchQuery.value,
    orderByField: 'id',
    orderBy: 'desc',
    limit: rowPerPage.value,
    page: currentPage.value,
    state_id: state_id.value
  }

  isRequestOngoing.value = true

  await circuitsStores.fetchCircuits(data)
  circuits.value = circuitsStores.getCircuits
  totalPages.value = circuitsStores.last_page
  totalCircuits.value = circuitsStores.circuitsTotalCount

  if(listParishes.value.length === 0) {
    await statesStores.fetchStates();
    await citiesStores.fetchCities();
    await municipalitiesStores.fetchMunicipalities();
    await parishesStores.fetchParishes();

    loadStates()
    loadCities()
    loadMunicipalities()
    loadParishes()
  }

  isRequestOngoing.value = false
}

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
            id: selectedCircuit.value.id,
        }

        circuitsStores.showCircuit(data)
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

const seeCircuit = circuitData => {
  router.push({ name : 'dashboard-admin-circuits-id', params: { id: circuitData.id } })
}

const submitForm = async (circuit, method) => {
  isRequestOngoing.value = true

  if (method === 'update') {
    submitUpdate(circuit)
    return
  }

  submitCreate(circuit.data)
}

const submitCreate = circuitData => {

  circuitsStores.addCircuit(circuitData)
    .then((res) => {
      if (res.data.success) {
        advisor.value = {
          type: 'success',
          message: 'Circuito creado con éxito',
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

const submitUpdate = circuitData => {

  circuitsStores.updateCircuit(circuitData.data, circuitData.id)
    .then((res) => {
      if (res.data.success) {
        advisor.value = {
          type: 'success',
          message: 'Circuito actualizado con éxito',
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


const editCircuit = circuitData => {
    isAddNewCircuitDrawerVisible.value = true
    selectedCircuit.value = { ...circuitData }
}

const showDeleteDialog = circuitData => {
  isConfirmDeleteDialogVisible.value = true
  selectedCircuit.value = { ...circuitData }
}

const removeCircuit = async () => {
  isConfirmDeleteDialogVisible.value = false
  let res = await circuitsStores.deleteCircuit({ ids: [selectedCircuit.value.id] })
  selectedCircuit.value = {}

  advisor.value = {
    type: res.data.success ? 'success' : 'error',
    message: res.data.success ? 'Circuito eliminado con éxito!' : res.data.message,
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
    state_id: state_id.value,
    limit: -1
  }

  await circuitsStores.fetchCircuits(data)

  let dataArray = [];
  
  circuitsStores.getCircuits.forEach(element => {
    let data = {
      NOMBRE: element.name,
      ESTADO: element.parish.municipality.state.name,
      MUNICIPIO: element.parish.municipality.name,
      PARROQUIA: element.parish.name,
      CIUDAD: element.city?.name
    }

    dataArray.push(data)
  })

  excelParser()
    .exportDataFromJSON(dataArray, "circuits", "csv");

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

        <VCard title="Filtros">
          <VCardText>
            <VRow>
              <VCol
                cols="12"
                sm="4"
              >
                <VSelect
                  v-model="state_id"
                  label="Estados"
                  :items="listStates"
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

            <v-spacer />

            <div class="d-flex align-center flex-wrap gap-4">
              <!-- 👉 Add user button -->
              <v-btn
                v-if="$can('crear','circuitos')"
                prepend-icon="tabler-plus"
                @click="isAddNewCircuitDrawerVisible = true">
                  Agregar Circuito
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
                <th scope="col"> ESTADO </th>
                <th scope="col"> UBICACIÓN </th>
                <th scope="col"> CIUDAD </th>
                <th scope="col" v-if="$can('ver','circuitos') || $can('editar','circuitos') || $can('eliminar','circuitos')">
                  ACCIONES
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="circuit in circuits"
                :key="circuit.id"
                style="height: 3.75rem;">

                <td> {{circuit.id }} </td>
                <td class="text-base font-weight-medium mb-0"> {{circuit.name }} </td>
                <td class="text-uppercase"> {{circuit.parish.municipality.state.name }} </td>
                <td class="text-wrap"> 
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base font-weight-medium mb-0">
                        {{circuit.parish.municipality.name }}
                      </h6>
                      <span class="text-disabled text-sm"> {{circuit.parish.name }}</span>
                    </div>
                  </div>
                </td>
                <td class="text-uppercase"> {{ circuit.city?.name }} </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('ver','circuitos') || $can('editar','circuitos') || $can('eliminar','circuitos')">      
                  <VBtn
                    v-if="$can('ver','circuitos')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="seeCircuit(circuit)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-eye" />
                  </VBtn>

                  <VBtn
                    v-if="$can('editar','circuitos')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="editCircuit(circuit)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-edit" />
                  </VBtn>

                  <VBtn
                    v-if="$can('eliminar','circuitos')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="showDeleteDialog(circuit)">
                              
                    <VIcon
                      size="22"
                      icon="tabler-trash" />
                  </VBtn>
                </td>
              </tr>
            </tbody>
            <!-- 👉 table footer  -->
            <tfoot v-show="!circuits.length">
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

    <!-- 👉 Add New Circuit -->
    <AddNewCircuitDrawer
      v-if="listParishes.length > 0"
      v-model:isDrawerOpen="isAddNewCircuitDrawerVisible"
      :circuit="selectedCircuit"
      :states="listStates"
      :cities="listCities"
      :municipalities="listMunicipalities"
      :parishes="listParishes"
      @circuit-data="submitForm"/>

    <!-- 👉 Confirm Delete -->
    <VDialog
      v-model="isConfirmDeleteDialogVisible"
      persistent
      class="v-dialog-sm" >
      <!-- Dialog close btn -->
        
      <DialogCloseBtn @click="isConfirmDeleteDialogVisible = !isConfirmDeleteDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Eliminar Circuito">
        <VCardText>
          Está seguro de eliminar el circuito <strong>{{ selectedCircuit.name }}</strong>?.
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isConfirmDeleteDialogVisible = false">
              Cancelar
          </VBtn>
          <VBtn @click="removeCircuit">
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
    subject: circuitos
</route>