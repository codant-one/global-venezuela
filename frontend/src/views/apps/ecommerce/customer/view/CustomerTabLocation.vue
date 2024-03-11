<script setup>

const props = defineProps({
  customerData: {
    type: Object,
    required: true,
  }
})

</script>

<template>
    <VRow>
        <!-- SECTION Customer Details -->
        <VCol cols="12">
        <VCard v-if="props.customerData">
            <VCardText>
                <VList class="card-list mt-2">
                    <VListItem>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Estado:
                            <span class="text-body-2">
                                {{ props.customerData.parish.municipality.state.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Municipio:
                            <span class="text-body-2">
                                {{ props.customerData.parish.municipality.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Parroquia:
                            <span class="text-body-2">
                                {{ props.customerData.parish.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Circuito:
                            <span class="text-body-2">
                                {{ props.customerData.community_council?.circuit.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Consejo Comunal:
                            <span class="text-body-2">
                                {{ props.customerData.community_council?.name }}
                            </span>
                            </h6>
                        </VListItemTitle>
                        <VListItemTitle>
                            <h6 class="text-base font-weight-semibold">
                            Address:
                            <span class="text-body-2">
                                {{ props.customerData.address }}
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
