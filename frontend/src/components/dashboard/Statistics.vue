<script setup>

const props = defineProps({
  resident: {
    type: Number,
    required: true
  },

  isMarried: {
    type: Number,
    required: true
  },

  has_children: {
    type: Number,
    required: true
  },

  transient: {
    type: Number,
    required: true
  },
})

const cant_resident = ref(props.resident)

const cant_isMarried = ref(props.isMarried)

const cant_haschildren = ref(props.has_children)

const cant_transient = ref(props.transient)


const statistics = [
  {
    title: 'Residentes',
    stats: cant_resident.value,
    icon: 'tabler-home-check',
    color: 'success',
  },
  {
    title: 'Casados',
    stats: cant_isMarried.value,
    icon: 'tabler-hearts',
    color: 'error',
  },
  {
    title: 'Con hijos',
    stats: cant_haschildren.value,
    icon: 'tabler-users-group',
    color: 'info',
  },
  {
    title: 'Transeúntes',
    stats: cant_transient.value,
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
