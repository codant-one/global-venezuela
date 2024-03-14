<script setup>

import { ref } from "vue"
import { useCircuitsStores } from '@/stores/useCircuits'
import { excelParser } from '@/plugins/csv/excelParser'
import router from '@/router'

const route = useRoute()
const circuitsStores = useCircuitsStores()

const circuit = ref([])
const title = ref(null)

const communityCouncils = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalCommunityCouncils = ref(0)

const isRequestOngoing = ref(true)

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
})

watchEffect(fetchData)

async function fetchData() {

    isRequestOngoing.value = true

    if(Number(route.params.id)) {

        let data = {
            search: searchQuery.value,
            orderByField: 'id',
            orderBy: 'desc',
            limit: rowPerPage.value,
            page: currentPage.value
        }

        circuit.value = await circuitsStores.showCircuit(data, Number(route.params.id))
        title.value = "CONSEJOS COMUNALES DE '" + circuit.value.name + "'"

        communityCouncils.value = circuitsStores.getCommunityCouncils
        totalPages.value = circuitsStores.community_last_page
        totalCommunityCouncils.value = circuitsStores.communityCouncilsTotalCount
    }

    isRequestOngoing.value = false
}

const seeCommunityCouncil = communityCouncilData => {
  router.push({ name : 'dashboard-admin-community-councils-id', params: { id: communityCouncilData.id } })
}

const downloadCSV = async () => {

    isRequestOngoing.value = true

    let data = {
        limit: -1
    }

    await circuitsStores.showCircuit(data, Number(route.params.id))

    let dataArray = [];

    circuitsStores.getCommunityCouncils.forEach(element => {
        let data = {
            NOMBRE: element.name,
            CIRCUITO: element.circuit.name,
            ESTADO: element.circuit.parish.municipality.state.name,
            MUNICIPIO: element.circuit.parish.municipality.name,
            PARROQUIA: element.circuit.parish.name,
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
  
        <VCard :title="title" v-if="title">
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

                <VTextField
                    v-model="searchQuery"
                    label="Buscar"
                    placeholder="Buscar"
                    density="compact"
                />
            </VCardText>

            <v-divider />

            <v-table class="text-no-wrap">
                <!-- 👉 table head -->
                <thead>
                    <tr>
                        <th scope="col"> #ID </th>
                        <th scope="col"> NOMBRE </th>
                        <th scope="col"> ACCIONES </th>
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
                        <td class="text-center" style="width: 5rem;" v-if="$can('ver','circuitos')">      
                            <VBtn
                                v-if="$can('ver','circuitos')"
                                icon
                                size="x-small"
                                color="default"
                                variant="text"
                                @click="seeCommunityCouncil(communityCouncil)">
                                        
                                <VIcon
                                    size="22"
                                    icon="tabler-eye" />
                            </VBtn>
                        </td>
                    </tr>
                </tbody>
                <!-- 👉 table footer  -->
                <tfoot v-show="!circuit.community_councils.length === 0">
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
  </section>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: circuitos
</route>