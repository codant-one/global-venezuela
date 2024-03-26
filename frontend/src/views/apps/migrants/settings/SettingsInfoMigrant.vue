<script setup>

import { requiredValidator } from '@validators'

const props = defineProps({
  migrant: {
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
    process_saime:'0',
    years_in_country: 0,
    antecedents: '0',
    isMarried: '0',
    has_children: '0',
    passport_status: '0',
    children_number: 1
})

watchEffect(fetchData)

async function fetchData() {

  if(props.migrant) {

    infoDetail.value.country_id = props.migrant.country.id
    infoDetail.value.transient = props.migrant.transient.toString()
    infoDetail.value.resident = props.migrant.resident.toString()
    infoDetail.value.process_saime = props.migrant.process_saime.toString()
    infoDetail.value.years_in_country = props.migrant.years_in_country
    infoDetail.value.antecedents = props.migrant.antecedents.toString()
    infoDetail.value.passport_status = props.migrant.passport_status.toString()
    infoDetail.value.isMarried = props.migrant.isMarried.toString()
    infoDetail.value.has_children = props.migrant.has_children.toString()
    infoDetail.value.children_number = props.migrant.children_number ?? 1

    country_id.value = props.migrant.country.name
    countryOld_id.value = props.migrant.country_id
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
                Información del Migrante
              </div>
              <div class="v-card-subtitle">Información de regularidad del migrante.</div>
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
                  <td class="text-wrap w-20 w-md-50">¿Posee visa de transeúnte?</td>
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
                  <td class="text-wrap w-20 w-md-50">¿Posee cédula de residencia?</td>
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
                  <td class="text-wrap w-20 w-md-50">¿Ha tramitado el proceso de naturalización ante el SAIME?</td>
                  <td colspan="2">
                    <VRadioGroup v-model="infoDetail.process_saime" inline class="py-2">
                      <VRadio value="1"/>
                      <VSpacer />
                      <VRadio value="0" class="radio-custom"/>
                    </VRadioGroup>
                  </td>
                  <td></td>
                </tr>

                <tr>
                  <td class="text-wrap w-20 w-md-50">¿Tiene el pasaporte vigente?</td>
                  <td colspan="2">
                    <VRadioGroup v-model="infoDetail.passport_status" inline class="py-2">
                      <VRadio value="1"/>
                      <VSpacer />
                      <VRadio value="0" class="radio-custom"/>
                    </VRadioGroup>
                  </td>
                  <td></td>
                </tr>

                <tr>
                  <td class="text-wrap w-20 w-md-50">¿Cuantos años tiene en el país?</td>
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
                  <td class="text-wrap w-20 w-md-50">¿Posee antecedentes penales?</td>
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
                  <td class="text-wrap w-20 w-md-50">¿Está casado/a con un venezolano/a?</td>
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
                  <td class="text-wrap w-20 w-md-50">¿Tiene hijos con nacionalidad venezolana?</td>
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
        {{ migrant ? 'Actualizar' : 'Agregar' }}
      </VBtn>
    </div>
  </VForm>
</template>

<style scoped>
  .radio-custom {
    margin-right: 56px;
  }

  @media(min-width: 991px) { 
    .w-md-50{
      width:50%!important
    }
  }

  @media(max-width: 1024px) { 
    .radio-custom {
      margin-right: 0 !important;
    }

    .v-selection-control__input > .v-icon {
      font-size: 20px;
    }

    .v-radio-group .v-selection-control-group .v-radio:not(:last-child) {
      margin-inline-end: 0.3rem !important;
    }

    .v-icon-size-default {
      font-size: 20px !important;
    }
  }
</style>
