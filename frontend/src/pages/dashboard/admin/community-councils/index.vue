<script setup>

import AddNewCommunityCouncilDrawer from './AddNewCommunityCouncilDrawer.vue'
import router from '@/router'
import { useCommunityCouncilsStores } from '@/stores/useCommunityCouncils'
import { useMiscellaneousStores } from '@/stores/useMiscellaneous'
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'

const communityCouncilsStores = useCommunityCouncilsStores()
const miscellaneousStores = useMiscellaneousStores()

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

const circuit_id = ref(null)
const state_id = ref(null)
const stateOld_id = ref(null)
const municipality_id = ref(null)
const municipalityOld_id = ref(null)
const listStates = ref([])
const listMunicipalities = ref([])
const listCircuits = ref([])

const listMunicipalitiesByStates = ref([])
const listCircuitsByMunicipalities = ref([])

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
    page: currentPage.value,
    state_id: stateOld_id.value,
    municipality_id: municipalityOld_id.value,
    circuit_id: circuit_id.value
  }

  isRequestOngoing.value = true

  await communityCouncilsStores.fetchCommunityCouncils(data)
  communityCouncils.value = communityCouncilsStores.getCommunityCouncils
  totalPages.value = communityCouncilsStores.last_page
  totalCommunityCouncils.value = communityCouncilsStores.communityCouncilsTotalCount

  if(listCircuits.value.length === 0) {
    await miscellaneousStores.fetchData();
    loadData()
  }

  isRequestOngoing.value = false
}

const loadData = () => {
  listStates.value = miscellaneousStores.getData.states
  listCircuits.value = miscellaneousStores.getData.circuits
  listMunicipalities.value = miscellaneousStores.getData.municipalities
  listCircuits.value = miscellaneousStores.getData.circuits
}

const getCircuits = computed(() => {
  return listCircuitsByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

const getMunicipalities = computed(() => {
  return listMunicipalitiesByStates.value.map((state) => {
    return {
      title: state.name,
      value: state.id,
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

    listCircuitsByMunicipalities.value = listCircuits.value.filter(item => item.municipality_id === _municipality.id)
  }
}

const clearSearch = () => {
  searchQuery.value = null
  fetchData()
}

const clearState = () => {
  stateOld_id.value = null
  fetchData()
}

const clearMunicipality = () => {
  municipalityOld_id.value = null
  fetchData()
}

const clearCircuit = () => {
  circuit_id.value = null
  fetchData()
}

const submitForm = async (communityCouncil, method) => {
  isRequestOngoing.value = true

  if (method === 'update') {
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

  communityCouncilsStores.updateCommunityCouncil(communityCouncilData.data, communityCouncilData.id)
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
  let res = await communityCouncilsStores.deleteCommunityCouncil(selectedCommunityCouncil.value.id)
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

const seeCircuit = communityCouncilData => {
  router.push({ name : 'dashboard-admin-circuits-id', params: { id: communityCouncilData.circuit.id } })
}

const seeCommunityCouncil = communityCouncilData => {
  router.push({ name : 'dashboard-admin-community-councils-id', params: { id: communityCouncilData.id } })
}

const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    state_id: stateOld_id.value,
    municipality_id: municipalityOld_id.value,
    circuit_id: circuit_id.value,
    limit: -1
  }

  await communityCouncilsStores.fetchCommunityCouncils(data)

  let dataArray = [];
  
  communityCouncilsStores.getCommunityCouncils.forEach(element => {
    let data = {
      NOMBRE: element.name,
      CIRCUITO: element.circuit.name,
      ESTADO: element.circuit.municipality.state.name,
      MUNICIPIO: element.circuit.municipality.name,
      CIUDAD: element.circuit.city?.name
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
                  @click:clear="clearState"
                  clearable
                />
              </VCol>
              <VCol cols="12" sm="4">
                <VAutocomplete
                  v-model="municipality_id"
                  label="Municipios"
                  :items="getMunicipalities"
                  :menu-props="{ maxHeight: '200px' }"
                  @update:model-value="selectMunicipalities"
                  @click:clear="clearMunicipality"
                  clearable
                />
              </VCol>
              <VCol cols="12" sm="4">
                <VAutocomplete
                  v-model="circuit_id"
                  label="Circuitos Comunales"
                  :items="getCircuits"
                  :menu-props="{ maxHeight: '200px' }"
                  @click:clear="clearCircuit"
                  clearable
                />
              </VCol>
              <VCol cols="12" sm="8"></VCol>
              <VCol
                cols="12"
                sm="4"
              >
                <VTextField
                  v-model="searchQuery"
                  label="Buscar"
                  placeholder="Buscar"
                  density="compact"
                  @click:clear="clearSearch"
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

            <v-spacer />

            <div class="d-flex align-center flex-wrap gap-4">

              <VSpacer />
              <v-btn
                v-if="$can('crear','consejos-comunales')"
                prepend-icon="tabler-plus"
                @click="isAddNewCommunityCouncilDrawerVisible = true">
                  Agregar Consejo Comunal
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
                <th scope="col"> MUNICIPIO </th>
                <th scope="col"> CIRCUITO </th>
                <th scope="col" v-if="$can('ver','consejos-comunales') || $can('editar','consejos-comunales') || $can('eliminar','consejos-comunales')">
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
                <td class="text-uppercase"> {{communityCouncil.circuit.municipality.state.name }} </td>
                <td class="text-uppercase"> {{ communityCouncil.circuit.municipality.name }} </td>
                <td class="text-wrap"> 
                  <div class="d-flex align-center">
                    <div class="d-flex flex-column">
                      <h6 class="text-base font-weight-medium mb-0 text-primary cursor-pointer" @click="seeCircuit(communityCouncil)">
                        {{communityCouncil.circuit.name}}
                      </h6>
                      <span class="text-disabled text-sm">{{communityCouncil.circuit.city?.name }}</span>
                    </div>
                  </div>
                </td>
                <!-- 👉 Acciones -->
                <td class="text-center" style="width: 5rem;" v-if="$can('ver','consejos-comunales') || $can('editar','consejos-comunales') || $can('eliminar','consejos-comunales')">      
                  <VBtn
                    v-if="$can('ver','consejos-comunales')"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="seeCommunityCouncil(communityCouncil)">
                              
                    <VIcon
                        size="22"
                        icon="tabler-eye" />
                  </VBtn>
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
        </VCard>
      </v-col>
    </v-row>

    <!-- 👉 Add New Community Council -->
    <AddNewCommunityCouncilDrawer
      v-if="listCircuits.length > 0"
      v-model:isDrawerOpen="isAddNewCommunityCouncilDrawerVisible"
      :communityCouncil="selectedCommunityCouncil"
      :states="listStates"
      :municipalities="listMunicipalities"
      :circuits="listCircuits"
      @community-council-data="submitForm"/>

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