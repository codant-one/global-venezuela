<script setup>

import { useMigrantsStores } from '@/stores/useMigrants'
import { themeConfig } from '@themeConfig'
import axios from 'axios';
import CustomerBioPanel from '@/views/apps/ecommerce/customer/view/CustomerBioPanel.vue'
import CustomerTabDocuments from '@/views/apps/ecommerce/customer/view/CustomerTabDocuments.vue'
import CustomerTabLocation from '@/views/apps/ecommerce/customer/view/CustomerTabLocation.vue'
import CustomerTabInfo from '@/views/apps/ecommerce/customer/view/CustomerTabInfo.vue'

const route = useRoute()
const migrantsStores = useMigrantsStores()

const userTab = ref(null)
const migrant = ref(null)
const isRequestOngoing = ref(true)

const tabs = [
  { title: 'Documentos' },
  { title: 'Ubicación' },
  { title: 'Información del migrante' }
]

const advisor = ref({
  type: '',
  message: '',
  show: false
})

watchEffect(fetchData)

async function fetchData() {

  isRequestOngoing.value = true

  if(Number(route.params.id)) {
    migrant.value = await migrantsStores.showMigrant(Number(route.params.id))
  }

  isRequestOngoing.value = false
}


const closeAdvisor = () => {
    setTimeout(() => {
        advisor.value = {
        type: '',
        message: '',
        show: false
        }
    }, 3000)
}

const download = async (img) => {

    try {

        const response = await axios({
            url: themeConfig.settings.urlbase + 'proxy-document?file=' + img, // Asegúrate de cambiar esto por tu URL real
            method: 'GET',
            responseType: 'blob', // Importante para manejar la respuesta como un archivo binario
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', img);
        document.body.appendChild(link);
        link.click();

        link.parentNode.removeChild(link);
        window.URL.revokeObjectURL(url);
  
        advisor.value.type = 'success'
        advisor.value.show = true
        advisor.value.message = 'Descarga Exitosa!'

    } catch (error) {

        advisor.value.type = 'error'
        advisor.value.show = true
        advisor.value.message = 'Error al descargar la imagen:' + error
    }

    closeAdvisor()
}
</script>

<template>
  <div>
    <VAlert
      v-if="advisor.show"
      :type="advisor.type"
      class="mb-6">  
      {{ advisor.message }}
    </VAlert>

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

    <!-- 👉 Header  -->
    <div v-if="migrant" class="d-flex justify-space-between align-center flex-wrap gap-y-4 mb-6">
        <div>
            <div class="d-flex gap-2 align-center mb-2 flex-wrap">
                <h4 class="text-h4 font-weight-medium">
                    Migrante ID #{{ route.params.id }}
                </h4>
            </div>
        </div>
        <div class="d-flex gap-4">
            <VBtn
                variant="tonal"
                color="secondary"
                class="mb-2"
                :to="{ name: 'dashboard-admin-migrants' }"
                >
                Regresar
            </VBtn>
        </div>
    </div>
    <!-- 👉 Customer Profile  -->
    <VRow v-if="migrant">
        <VCol
            cols="12"
            md="5"
            lg="4"
        >
            <CustomerBioPanel :customer-data="migrant" />
        </VCol>
        <VCol
            cols="12"
            md="7"
            lg="8">
            <VTabs
                v-model="userTab"
                class="v-tabs-pill mb-3 disable-tab-transition">
                <VTab
                    v-for="tab in tabs"
                    :key="tab.title">
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>
            <VWindow
                v-model="userTab"
                class="disable-tab-transition"
                :touch="false"
            >
                <VWindowItem>
                    <CustomerTabDocuments 
                        :customer-data="migrant"
                        @download="download" />
                </VWindowItem>
                <VWindowItem>
                    <CustomerTabLocation :customer-data="migrant" />
                </VWindowItem>
                <VWindowItem>
                    <CustomerTabInfo :customer-data="migrant" />
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
  </div>
</template>

<route lang="yaml">
  meta:
    action: ver
    subject: migrantes
</route>