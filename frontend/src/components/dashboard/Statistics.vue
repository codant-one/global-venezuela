<script setup>
const statistics = [
  {
    title: 'Residentes',
    stats: '20.000',
    icon: 'tabler-home-check',
    color: 'success',
  },
  {
    title: 'Casados',
    stats: '8.549',
    icon: 'tabler-hearts',
    color: 'error',
  },
  {
    title: 'Con hijos',
    stats: '1.423',
    icon: 'tabler-users-group',
    color: 'info',
  },
  {
    title: 'Transeúntes',
    stats: '9.700',
    icon: 'tabler-user-up',
    color: 'info',
  },
]
</script>

<template>
  <VCard title="Estadísticas">
    <template #append>
      <span class="text-sm text-disabled">En el mes</span>
    </template>

    <VCardText class="pt-6">
      <VRow>
        <VCol
          v-for="item in statistics"
          :key="item.title"
          cols="6"
          md="3"
        >
          <div class="d-flex align-center gap-4">
            <VAvatar
              :color="item.color"
              variant="tonal"
              size="42"
            >
              <VIcon :icon="item.icon" />
            </VAvatar>

            <div class="d-flex flex-column">
              <span class="text-h5 font-weight-medium">{{ item.stats }}</span>
              <span class="text-sm">
                {{ item.title }}
              </span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>
