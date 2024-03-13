<script setup>

import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useMiscellaneousStores } from '@/stores/useMiscellaneous'
import { useVolunteersStores } from '@/stores/useVolunteers'

const miscellaneousStores = useMiscellaneousStores()
const volunteersStores = useVolunteersStores()

const volunteers = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalVolunteers = ref(0)
const isRequestOngoing = ref(true)

const state_id = ref(null)
const stateOld_id = ref(null)
const listStates = ref([])

const theme_id = ref(null)
const themeOld_id = ref(null)
const listThemes = ref([])

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = volunteers.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = volunteers.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Mostrando ${ firstIndex } hasta ${ lastIndex } de ${ totalVolunteers.value } registros`
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
    theme_id: themeOld_id.value,
    isState: true
  }

  isRequestOngoing.value = true

  await volunteersStores.fetchVolunteers(data)
  
  volunteers.value = volunteersStores.getVolunteers
  totalPages.value = volunteersStores.last_page
  totalVolunteers.value = volunteersStores.volunteersTotalCount

  if(listStates.value.length === 0) {
    await miscellaneousStores.fetchData();
    loadData()
  }

  isRequestOngoing.value = false
}

const clearSearch = () => {
  searchQuery.value = null
  fetchData()
}

const clearState = () => {
  stateOld_id.value = null
  fetchData()
}

const clearTheme = () => {
  themeOld_id.value = null
  fetchData()
}

const loadData = () => {
  listStates.value = miscellaneousStores.getData.states
  listThemes.value = miscellaneousStores.getData.themes
}

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state) 
    state_id.value = _state.name
    stateOld_id.value = _state.id
  }
}

const selectTheme = theme => {
  if (theme) {
    let _theme = listThemes.value.find(item => item.name === theme) 
    theme_id.value = _theme.name
    themeOld_id.value = _theme.id
  }
}

const downloadCSV = async () => {

  isRequestOngoing.value = true

  let data = {
    state_id: stateOld_id.value,
    theme_id: themeOld_id.value,
    isState: true,
    limit: -1
  }

  await volunteersStores.fetchVolunteers(data)

  let dataArray = [];
  
  volunteersStores.getVolunteers.forEach(element => {

    let data = {
      ESTADO: element.state.name,
      TRANSFORMACIÓN: element.theme.name,
      NOMBRE: element.name ?? '',
      DOCUMENTO: element.document ?? '',
      TELÉFONO: element.phone ?? '',
      E_MAIL: element.email ?? '',
      ES_RESPONSABLE: element.isResponsible ? 'SI' : ''
    }

    dataArray.push(data)
  })

  excelParser()
    .exportDataFromJSON(dataArray, "volunteers-states", "csv");

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
        <VCard title="Filtros">
            <VCardText>
                <VRow>
                    <VCol cols="12" sm="4">
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
                        v-model="theme_id"
                        label="Transformaciones"
                        :items="listThemes"
                        item-title="name"
                        item-value="name"
                        :menu-props="{ maxHeight: '200px' }"
                        @update:model-value="selectTheme"
                        @click:clear="clearTheme"
                        clearable
                      />
                    </VCol>
                    <VCol cols="12" sm="4">
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

            </VCardText>

          <v-divider />

          <v-table class="text-no-wrap">
            <!-- 👉 table head -->
            <thead>
              <tr>
                <th scope="col"> #ID </th>
                <th scope="col"> ESTADO </th>
                <th scope="col"> TRANSFORMACIÓN </th>
                <th scope="col"> NOMBRE </th>
                <th scope="col"> DOCUMENTO </th>
                <th scope="col"> TELÉFONO </th> 
                <th scope="col"> RESPONSABLE </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="volunteer in volunteers"
                :key="volunteer.id"
                style="height: 3.75rem;">

                <td> {{volunteer.id }} </td>
                <td> {{volunteer.state.name }} </td>
                <td> {{volunteer.theme.name }} </td>
                <td> {{volunteer.name }} </td>
                <td> {{volunteer.document }} </td>
                <td> {{volunteer.phone }} </td>
                <td>
                  <VChip v-if="volunteer.isResponsible" color="primary">SI</VChip>
                  <VChip v-else color="error">NO</VChip>
                </td>
              </tr>
            </tbody>
            <!-- 👉 table footer  -->
            <tfoot v-show="!volunteers.length">
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
    subject: voluntarios
</route>