<script setup>

import router from '@/router'
import Toaster from "@/components/common/Toaster.vue";
import { ref } from "vue"
import { excelParser } from '@/plugins/csv/excelParser'
import { useReportsStores } from '@/stores/useReports'
import { useUsersStores } from '@/stores/useUsers'
import { avatarText } from '@/@core/utils/formatters'
import { themeConfig } from '@themeConfig'

const reportsStores = useReportsStores()
const usersStores = useUsersStores()

const migrants = ref([])
const searchQuery = ref('')
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPages = ref(1)
const totalMigrants = ref(0)
const isRequestOngoing = ref(true)

const listUsers = ref([])
const user_id = ref(null)

const IdsUserOnline = ref([])
const userOnline = ref([])
let interval = null

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

onMounted(()=>{
    interval = setInterval(()=>{
        onlineList()
    }, 60000)
})

onUnmounted(()=>{
    clearInterval(interval)
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
        user_id: user_id.value
    }

    isRequestOngoing.value = true

    await reportsStores.fetchMigrants(data)
    
    migrants.value = reportsStores.getMigrants
    totalPages.value = reportsStores.last_page
    totalMigrants.value = reportsStores.migrantsTotalCount

    await usersStores.fetchUsers({ limit: -1 })

    listUsers.value = usersStores.getUsers.map(user => ({
        ...user,
        fullname: `${user.name} ${user.last_name}`
    }));

    IdsUserOnline.value = []
    
    listUsers.value.forEach(element => {
        IdsUserOnline.value.push(element.id)
    }) 

    onlineList()

    isRequestOngoing.value = false
}

const seeMigrant = migrantData => {
    router.push({ name : 'dashboard-admin-migrants-id', params: { id: migrantData.id } })
}

const online = id =>{

    let uo = userOnline.value.find(user => user.id == id )
    let current = new Date()
    let online = new Date(2000, 0, 1, 12, 0, 0)

    if(uo && uo.online!=null)
        online = new Date(uo.online)

    let gapSeconds = Math.abs((online.getTime() - current.getTime()) / 1000)

    if(gapSeconds>120) {
        return 'error'
    } else {
        return 'success'
    }  
}

const onlineList = () => {
    return new Promise((resolve, reject) => {

        let params = {
        ids: IdsUserOnline.value.join(',')
        }

        usersStores.getUsersOnline(params)
        .then(response => {
            userOnline.value = response
            resolve()
        }).catch(error => {})

    })
}

const downloadCSV = async () => {

    isRequestOngoing.value = true

    let data = { 
        orderByField: 'user_id',
        orderBy: 'asc',
        user_id: user_id.value,
        limit: -1 
    }

    await reportsStores.fetchMigrants(data)

    let dataArray = [];
    
    reportsStores.getMigrants.forEach(element => {

        let data = {
            REGISTRADO_POR: element.user.name + ' ' + (element.user.last_name ?? '') + ': ' + element.user.email,
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
                            sm="4"
                        >
                            <VAutocomplete
                                v-model="user_id"
                                label="Usuarios"
                                :items="listUsers"
                                item-value="id"
                                item-title="fullname"
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
                            <th scope="col"> REGISTRADO POR </th>
                            <th scope="col"> NOMBRE </th>
                            <th scope="col"> TELÉFONO </th>
                            <th scope="col"> E-MAIL </th>
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
                            <td> {{migrant.id }} </td>
                            <td>  
                                <div class="d-flex align-center">
                                    <VBadge
                                        dot
                                        location="bottom right"
                                        offset-x="3"
                                        offset-y="3"
                                        bordered
                                        :color="online(migrant.user.id)"
                                    >
                                        <VAvatar
                                            variant="tonal"
                                            size="38"
                                        >
                                            <VImg
                                            v-if="migrant.user.avatar"
                                            style="border-radius: 50%;"
                                            :src="themeConfig.settings.urlStorage + migrant.user.avatar"
                                            />
                                            <span v-else>{{ avatarText(migrant.user.name) }}</span>
                                        </VAvatar>
                                    </VBadge>
                                    <div class="ml-3 d-flex flex-column">
                                        <span class="text-body-1 font-weight-medium text-uppercase"> {{ migrant.user.name }}  {{ migrant.user.last_name ?? '' }}</span>
                                        <span class="text-sm text-disabled"> {{ migrant.user.email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-wrap text-base font-weight-medium mb-0"> {{migrant.name }}  {{migrant.last_name }} </td>
                            <td> {{migrant.phone }} </td>
                            <td> {{migrant.email }} </td>
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

<route lang="yaml">
  meta:
    action: ver
    subject: reportes
</route>