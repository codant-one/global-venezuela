<script setup>

const props = defineProps({
  customerData: {
    type: Object,
    required: true,
  }
})

watchEffect(fetchData)

async function fetchData() {
    props.customerData.transient = props.customerData.transient ? '1' : '0'
    props.customerData.resident = props.customerData.resident ? '1' : '0'
    props.customerData.antecedents = props.customerData.antecedents ? '1' : '0'
    props.customerData.isMarried = props.customerData.isMarried ? '1' : '0'
    props.customerData.passport_status = props.customerData.passport_status ? '1' : '0'
    props.customerData.has_children = props.customerData.has_children ? '1' : '0'
}

</script>

<template>
    <VRow>
        <!-- SECTION Customer Details -->
        <VCol cols="12">
        <VCard v-if="props.customerData">
            <VCardText>
                <VTable class="text-no-wrap mb-6 border rounded">
                    <thead>
                        <tr>
                        <th scope="col"></th>
                        <th scope="col">SI</th>
                        <th scope="col">NO</th>
                        <th scope="col">CANTIDAD</th>
                        </tr>
                    </thead>
                    <tbody>  
                        <tr>
                            <td width="400px">¿Posee cédula de transeúnte?</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.transient" inline class="py-2" readonly>
                                <VRadio value="1"/>
                                <VSpacer />
                                <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td width="400px">¿Posee cédula de residencia?</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.resident" inline class="py-2" readonly>
                                    <VRadio value="1"/>
                                    <VSpacer />
                                    <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td width="400px">¿Tiene el pasaporte vigente</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.passport_status" inline class="py-2" readonly>
                                <VRadio value="1"/>
                                <VSpacer />
                                <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td width="400px">¿Cuantos años tiene el país?</td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="text-body-2">
                                    {{ props.customerData.years_in_country }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td width="400px">¿Posee antecedentes penales?</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.antecedents" inline class="py-2" readonly>
                                <VRadio value="1"/>
                                <VSpacer />
                                <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td width="400px">¿Está casado/a con un venezolano/a?</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.isMarried" inline class="py-2" readonly>
                                <VRadio value="1"/>
                                <VSpacer />
                                <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td width="400px">¿Tiene hijos con nacionalidad venezolana?</td>
                            <td colspan="2">
                                <VRadioGroup v-model="props.customerData.has_children" inline class="py-2" readonly>
                                <VRadio value="1"/>
                                <VSpacer />
                                <VRadio value="0" class="radio-custom"/>
                                </VRadioGroup>
                            </td>
                            <td v-if="props.customerData.has_children === '1'">
                                <span class="text-body-2">
                                    {{ props.customerData.children_number }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    </VTable>
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
