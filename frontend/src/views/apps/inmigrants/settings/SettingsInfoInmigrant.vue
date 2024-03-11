<script setup>

import { requiredValidator } from '@validators'

const props = defineProps({
  inmigrant: {
    type: Object,
    required: false
  },
  countries: {
    question: Object,
    required: true
  }
})

const emit = defineEmits([
  'submit',
  'back'
])

const listCountries = ref(props.countries)
const country_id = ref('')
const countryOld_id = ref('')
const refVForm = ref()

const infoDetail = ref({
    country_id: '',
    transient: '0',
    resident: '0',
    years_in_country: 0,
    antecedents: '0',
    isMarried: '0',
    has_children: '0',
    children_number: 1
})

watchEffect(fetchData)

async function fetchData() {

  if(props.inmigrant) {

    infoDetail.value.country_id = props.inmigrant.country.name
    infoDetail.value.transient = props.inmigrant.transient.toString()
    infoDetail.value.resident = props.inmigrant.resident.toString()
    infoDetail.value.years_in_country = props.inmigrant.years_in_country
    infoDetail.value.antecedents = props.inmigrant.antecedents.toString()
    infoDetail.value.isMarried = props.inmigrant.isMarried.toString()
    infoDetail.value.has_children = props.inmigrant.has_children.toString()
    infoDetail.value.children_number = props.inmigrant.children_number

    countryOld_id.value = props.inmigrant.country_id
  }
}


const selectCountry = country => {
  if (country) {
    let _country = listCountries.value.find(item => item.name === country)
    infoDetail.value.country_id = _country.id
  }
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

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {
      emit('submit', infoDetail.value)
    }
  })
}

</script>

<template>
  <VForm
      ref="refVForm"
      @submit.prevent="onSubmit"
    >
    <VCard class="mb-6">
      <VCardItem>
        <VCardTitle>
          <div class="d-flex flex-wrap align-center mb-4">
            <VIcon
              icon="tabler-world"
              size="30"
              class="me-4"
            />

            <div>
              <div class="text-high-emphasis">
                Información del Inmigrante
              </div>
              <div class="v-card-subtitle">Información de regularidad del inmigrante.</div>
            </div>
          </div>
        </VCardTitle>
      </VCardItem>

      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="country_id"
              label="País de origen"
              :rules="[requiredValidator]"
              :items="listCountries"
              item-title="name"
              item-value="name"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectCountry"
            >
              <template
                v-if="country_id"
                #prepend
                >
                <VAvatar
                  start
                  style="margin-top: -8px;"
                  size="36"
                  :image="getFlagCountry(country_id)"
                />
              </template>
            </VAutocomplete>
          </VCol>
          <VCol cols="12">
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
                    <VRadioGroup v-model="infoDetail.transient" inline class="py-2">
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
                    <VRadioGroup v-model="infoDetail.resident" inline class="py-2">
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
                    <VTextField
                      v-model="infoDetail.years_in_country"
                      type="number"
                      :rules="[requiredValidator]"
                      style="width: 80px;"
                      min="0"
                    />
                  </td>
                </tr>

                <tr>
                  <td width="400px">¿Posee antecedentes penales?</td>
                  <td colspan="2">
                    <VRadioGroup v-model="infoDetail.antecedents" inline class="py-2">
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
                    <VRadioGroup v-model="infoDetail.isMarried" inline class="py-2">
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
                    <VRadioGroup v-model="infoDetail.has_children" inline class="py-2">
                      <VRadio value="1"/>
                      <VSpacer />
                      <VRadio value="0" class="radio-custom"/>
                    </VRadioGroup>
                  </td>
                  <td v-if="infoDetail.has_children === '1'">
                    <VTextField
                      v-model="infoDetail.children_number"
                      type="number"
                      :rules="[requiredValidator]"
                      style="width: 80px;"
                      min="1"
                    />
                  </td>
                </tr>
              </tbody>
            </VTable>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <div class="d-flex justify-end gap-x-4">
      <VBtn
        color="secondary"
        variant="tonal"
        @click="emit('back')"
      >
        Atrás
      </VBtn>
      <VBtn type="submit" color="primary">
        Agregar
      </VBtn>
    </div>
  </VForm>
</template>

<style scoped>
  .radio-custom {
    margin-right: 56px;
  }
</style>
