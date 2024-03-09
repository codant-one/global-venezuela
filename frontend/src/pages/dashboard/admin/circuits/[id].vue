<script setup>

import { ref } from "vue"
import { useCircuitsStores } from '@/stores/useCircuits'
import router from '@/router'

const route = useRoute()
const circuitsStores = useCircuitsStores()

const circuit = ref([])
const title = ref(null)
const isRequestOngoing = ref(true)

watchEffect(fetchData)

async function fetchData() {

    isRequestOngoing.value = true

    if(Number(route.params.id)) {
        circuit.value = await circuitsStores.showCircuit(Number(route.params.id))
        title.value = "Consejos Comunales de " + circuit.value.name

    }

    isRequestOngoing.value = false
}

const seeCommunityCouncil = communityCouncilData => {
  router.push({ name : 'dashboard-admin-community-councils-id', params: { id: communityCouncilData.id } })
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
                        v-for="communityCouncil in circuit.community_councils"
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
        </VCard>
  </section>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: circuitos
</route>