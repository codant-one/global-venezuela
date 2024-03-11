<script setup>

import { ref } from "vue"
import { useCommunityCouncilsStores } from '@/stores/useCommunityCouncils'
import { excelParser } from '@/plugins/csv/excelParser'
import router from '@/router'

const route = useRoute()
const communityCouncilsStores = useCommunityCouncilsStores()

const communityCouncil = ref([])
const title = ref(null)

const inmigrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalInmigrants = ref(0)

const isRequestOngoing = ref(true)

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

    isRequestOngoing.value = true

    if(Number(route.params.id)) {

        let data = {
            search: searchQuery.value,
            orderByField: 'id',
            orderBy: 'desc',
            limit: rowPerPage.value,
            page: currentPage.value
        }

        communityCouncil.value = await communityCouncilsStores.showCommunityCouncil(data, Number(route.params.id))
        title.value = "Inmigrantes en " + communityCouncil.value.name

        inmigrants.value = communityCouncilsStores.getInmigrants
        totalPages.value = communityCouncilsStores.inmigrant_last_page
        totalInmigrants.value = communityCouncilsStores.inmigrantsTotalCount

    }

    isRequestOngoing.value = false
}

const seeInmigrant = inmigrantData => {
  router.push({ name : 'dashboard-admin-inmigrants-id', params: { id: inmigrantData.id } })
}

const downloadCSV = async () => {

    isRequestOngoing.value = true

    let data = {
        limit: -1
    }

    await await communityCouncilsStores.showCommunityCouncil(data, Number(route.params.id))

    let dataArray = [];

    communityCouncilsStores.getInmigrants.forEach(element => {

        let data = {
            NOMBRE: element.name,
            APELLIDO: element.last_name,
            EMAIL: element.email,
            FECHA_NACIMIENTO: element.birthdate,
            PAÍS_INMIGRANTE: element.country.name,
            GÉNERO: element.gender.name,
            NÚMERO_PASAPORTE: element.passport_number,
            PASAPORTE_VIGENTE: element.passport_status ? 'SI' : 'NO',
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
                        <th scope="col"> TELÉFONO </th>
                        <th scope="col"> EMAIL </th>
                        <th scope="col"> DIRECCIÓN </th>
                        <th scope="col"> ACCIONES </th>
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
                        <td class="text-center" style="width: 5rem;" v-if="$can('ver','circuitos')">      
                            <VBtn
                                v-if="$can('ver','circuitos')"
                                icon
                                size="x-small"
                                color="default"
                                variant="text"
                                @click="seeInmigrant(inmigrant)">
                                        
                                <VIcon
                                    size="22"
                                    icon="tabler-eye" />
                            </VBtn>
                        </td>
                    </tr>
                </tbody>
                <!-- 👉 table footer  -->
                <tfoot v-show="!inmigrants.length === 0">
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
    subject: consejos-comunales
</route>