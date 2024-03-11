<script setup>

import { themeConfig } from '@themeConfig'
import { avatarText, formatNumber } from '@/@core/utils/formatters'
import { useCountriesStores } from '@/stores/useCountries'
// import rocketImg from '@images/eCommerce/rocket.png'

const props = defineProps({
  customerData: {
    type: Object,
    required: true,
  }
})

const route = useRoute()
const countriesStores = useCountriesStores()

const isUpgradePlanDialogVisible = ref(false)

const listCountries = ref([])
const icon = ref('tabler-shopping-cart')

watchEffect(fetchData)

async function fetchData() {
  await countriesStores.fetchCountries();

  loadCountries()
}

const loadCountries = () => {
  listCountries.value = countriesStores.getCountries
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
        <VCardText class="text-center pt-15">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded
            :size="100"
            color="primary"
          >
            <span
              class="text-5xl font-weight-medium"
            >
              {{ avatarText(props.customerData.name + ' ' + props.customerData.last_name) }}
            </span>
          </VAvatar>

          <!-- 👉 Customer fullName -->
          <h4 class="text-h4 mt-4">
            {{ props.customerData.name }}  {{ props.customerData.last_name ?? '' }}
          </h4>
          <span class="text-sm">Inmigrante ID #{{ props.customerData.id }}</span>
        </VCardText>

        <!-- 👉 Customer Details -->
        <VCardText>
          <VDivider class="my-4" />
          <div class="text-disabled text-uppercase text-sm">
            Detalles
          </div>

          <VList class="card-list mt-2">
            <VListItem>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                  Nombre:
                  <span class="text-body-2">
                    {{ props.customerData.name }}
                  </span>
                </h6>
              </VListItemTitle>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                  Apellido:
                  <span class="text-body-2">
                    {{ props.customerData.last_name ?? '' }}
                  </span>
                </h6>
              </VListItemTitle>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                  Email:
                  <span class="text-body-2">
                    {{ props.customerData.email }}
                  </span>
                </h6>
              </VListItemTitle>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                    Teléfono:
                  <span class="text-body-2">
                    {{ props.customerData.phone }}
                  </span>
                </h6>
              </VListItemTitle>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                  Fecha de Nacimiento:
                  <span class="text-body-2">
                    {{ props.customerData.birthdate }}
                  </span>
                </h6>
              </VListItemTitle>
              <VListItemTitle>
                <h6 class="text-base font-weight-semibold">
                  Género:
                  <span class="text-body-2 me-2">
                    {{ props.customerData.gender.name }}
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
