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

const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')

const listStates = ref([])
const listMunicipalities = ref([])
const listParishes = ref([])

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])

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
        theme_id: themeOld_id.value,
        state_id: stateOld_id.value,
        municipality_id: municipalityOld_id.value,
        parish_id: parishOld_id.value,
        isParish: true
    }

    isRequestOngoing.value = true

    await volunteersStores.fetchVolunteers(data)
    
    volunteers.value = volunteersStores.getVolunteers
    totalPages.value = volunteersStores.last_page
    totalVolunteers.value = volunteersStores.volunteersTotalCount

    if(listMunicipalities.value.length === 0) {
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
    stateOld_id.value = ''
    municipality_id.value = ''
    municipalityOld_id.value = ''
    parish_id.value = ''
    parishOld_id.value = ''
    fetchData()
}

const clearMunicipality = () => {
    municipality_id.value = ''
    municipalityOld_id.value = ''
    parish_id.value = ''
    parishOld_id.value = ''
    fetchData()
}

const clearParish = () => {
    parish_id.value = ''
    parishOld_id.value = ''
    fetchData()
}

const clearTheme = () => {
    themeOld_id.value = null
    fetchData()
}


const loadData = () => {
    listThemes.value = miscellaneousStores.getData.themes
    listStates.value = miscellaneousStores.getData.states
    listMunicipalities.value = miscellaneousStores.getData.municipalities
    listParishes.value = miscellaneousStores.getData.parishes

    let newTheme = {id: 8, name: 'OTRO'};

    listThemes.value.unshift(newTheme);
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
    theme_id: themeOld_id.value,
    state_id: stateOld_id.value,
    municipality_id: municipalityOld_id.value,
    limit: -1,
    isParish: true
  }

  await volunteersStores.fetchVolunteers(data)

  let dataArray = [];
  
  volunteersStores.getVolunteers.forEach(element => {

    let data = {
      ESTADO: element.parish.municipality.state.name,
      MUNICIPIO: element.parish.municipality.name,
      PARROQUIA: element.parish.name,
      CONSEJO_COMUNAL: element.community_council.name,
      TRANSFORMACIÓN: element.theme.name,
      NOMBRE: element.name ?? '',
      DOCUMENTO: element.document ?? '',
      TELÉFONO: element.phone ?? '',
      E_MAIL: element.email ?? '',
      DIRECCIÓN: element.address ?? '',
    }
  
    dataArray.push(data)
  })

  excelParser()
    .exportDataFromJSON(dataArray, "volunteers-independents", "csv");

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
                            v-model="parish_id"
                            label="Parroquias"
                            :items="getParishes"
                            :menu-props="{ maxHeight: '200px' }"
                            @update:model-value="selectParishes"
                            @click:clear="clearParish"
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
                    <VCol cols="12" sm="4"></VCol>
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
                <th scope="col"> MUNICIPIO </th>
                <th scope="col"> PARROQUIA </th>
                <th scope="col"> CONSEJO COMUNAL </th>
                <th scope="col"> TRANSFORMACIÓN </th>
                <th scope="col"> NOMBRE </th>
                <th scope="col"> DOCUMENTO </th>
                <th scope="col"> TELÉFONO </th> 
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr 
                v-for="volunteer in volunteers"
                :key="volunteer.id"
                style="height: 3.75rem;">
                <td> {{volunteer.id }} </td>
                <td> {{volunteer.parish.municipality.state.name }} </td>
                <td> {{volunteer.parish.municipality.name }} </td>
                <td class="text-wrap text-base font-weight-medium mb-0"> {{ volunteer.parish.name }} </td>
                <td class="text-wrap text-base font-weight-medium mb-0"> {{ volunteer.community_council?.name }} </td>
                <td class="text-wrap"> {{volunteer.theme.name }} </td>
                <td class="text-wrap"> {{volunteer.name }} </td>
                <td class="text-wrap"> {{volunteer.document }} </td>
                <td class="text-wrap"> {{volunteer.phone }} </td>
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