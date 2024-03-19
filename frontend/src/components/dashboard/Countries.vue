<script setup>

const props = defineProps({
  countries: {
    type: Object,
    required: true
  },
})

const countries_ = ref(props.countries)
const sum = ref(0)
const subtitle = ref(null)

watchEffect(fetchData)

async function fetchData() {

    countries_.value.forEach( function  (element) { 
        sum.value = sum.value + Number.parseInt(element.total)
    });

    subtitle.value = `Total de ${sum.value} migrantes en el top 5`
}

const getFlagCountry = country => {
  let val = countries_.value.find(item => {
    return item.name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") === country.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  })

  if(val)
    return 'https://hatscripts.github.io/circle-flags/flags/'+val.iso.toLowerCase()+'.svg'
  else
    return ''
}
</script>

<template>
  <VCard
    title="TOP 5 de Paises"
    :subtitle="subtitle"
  >
    <VCardText>
      <VList class="card-list">
        <VListItem
          v-for="(country, index) in countries_"
          v-show="index < 5"
          :key="country.name"
        >
            <template #prepend>
                <VAvatar
                    start
                    style="margin-top: -8px;"
                    size="36"
                    :image="getFlagCountry(country.name)"
                />
            </template>

            <VListItemTitle class="font-weight-medium">
                {{ country.name }}
            </VListItemTitle>
            <VListItemSubtitle class="text-disabled">
                {{ country.percentage }}% migrantes
            </VListItemSubtitle>

            <template #append>
                <div class="d-flex align-center">
                <span class="font-weight-medium text-medium-emphasis me-2">{{ country.total }}</span>
                </div>
            </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>
