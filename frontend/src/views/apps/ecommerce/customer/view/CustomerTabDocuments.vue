<script setup>

import { useCountriesStores } from '@/stores/useCountries'

const props = defineProps({
  customerData: {
    type: Object,
    required: true,
  }
})

const countriesStores = useCountriesStores()

const emit = defineEmits([
    'download'
])

const listCountries = ref([])
const document = ref(null)
const icon_type = ref(null)
const file = ref('')

const loadCountries = () => {
  listCountries.value = countriesStores.getCountries
}

watchEffect(fetchData)

async function fetchData() {

    await countriesStores.fetchCountries();
    loadCountries()
    
    if(props.customerData.document !== null) {
    
        if(props.customerData.file_document) {
            document.value = props.customerData.file_document.split('migrants/')[1]

            switch (document.value.split('.')[1]) {
                case 'pdf':
                    icon_type.value = 'tabler-file-type-pdf'
                    break;
                case 'docx':
                    icon_type.value = 'mdi-file-word'
                    break;
                case 'doc':
                    icon_type.value = 'mdi-file-word'
                    break;
                case 'jpg':
                    icon_type.value = 'tabler-file-type-jpg'
                    break;
                default:
                    icon_type.value = 'tabler-file-type-png'
                    break;
            }
        }
    }

}

const download = () => {

    file.value = props.customerData.file_document === null ? '' : props.customerData.file_document

    let data = {
        icon: document.value.split('.')[1],
        document: file.value
    }
    
    emit('download', data)
}

const getFlagCountry = country => {
  let val = listCountries.value.find(item => {
    return item.name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") === country.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  })

  if(val)
    return 'https://hatscripts.github.io/circle-flags/flags/'+val.iso.toLowerCase()+'.svg'
  else
    return ''
}

</script>

<template>
    <VRow>
        <!-- SECTION Customer Details -->
        <VCol cols="12">
        <VCard v-if="props.customerData">
            <VCardText class="text-center pt-15" v-if="icon_type">
                <!-- 👉 Avatar -->
                <VAvatar
                    rounded
                    :size="100"
                    color="primary"
                    class="cursor-pointer"
                    @click="download()"
                >
                    <VTooltip
                        open-on-focus
                        location="top"
                        activator="parent"
                        text="Descargar">
                        <template v-slot:activator="{ props }">
                            <VIcon color="white" :icon="icon_type" size="x-large" />
                        </template>
                    </VTooltip>
                    
                </VAvatar>

            </VCardText>

            <!-- 👉 Customer Details -->
            <VCardText>
                <VDivider class="my-4" v-if="icon_type"/>
                <VList class="card-list mt-2">
                    <VListItem>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                                País de origen:
                            <span class="text-body-2">
                                <VAvatar
                                    start
                                    style="margin-top: -8px;"
                                    size="25"
                                    :image="getFlagCountry(props.customerData.country.name)"
                                />
                                {{ props.customerData.country.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                    </VListItem>
                    <VListItem>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                                Documento:
                            <span class="text-body-2">
                                {{ props.customerData.passport_number }}
                            </span>
                            </h6>
                        </VListItemTitle>
                    </VListItem>
                </VList>
            </VCardText>

        </VCard>
        </VCol>
        <!-- !SECTION -->
    </VRow>
 </template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 0.75rem;
}

.current-plan{
  background: linear-gradient(45deg, rgb(var(--v-theme-primary)) 0%, #9E95F5 100%);
  color: #fff;
}
</style>
