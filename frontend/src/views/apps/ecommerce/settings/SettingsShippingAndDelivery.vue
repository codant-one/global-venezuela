<script setup>
import usflag from '@images/icons/countries/us.png'

const domesticTableData = [
  {
    rate: 'Weight',
    condition: '5Kg-10Kg',
    price: '$9',
  },
  {
    rate: 'VAT',
    condition: '12%',
    price: '$25',
  },
  {
    rate: 'Duty',
    condition: '-',
    price: '-',
  },
]

const InternationalTableData = [
  {
    rate: 'Weight',
    condition: '5Kg-10Kg',
    price: '$9',
  },
  {
    rate: 'VAT',
    condition: '12%',
    price: '$25',
  },
  {
    rate: 'Duty',
    condition: 'Japan',
    price: '$49',
  },
]
</script>

<template>
  <VCard class="mb-6">
    <VCardItem
      title="Shipping Zone"
      subtitle="Choose where you ship and how much you charge for shipping at checkout."
    >
      <template #append>
        <VBtn variant="text">
          Create Zone
        </VBtn>
      </template>
    </VCardItem>

    <VCardText>
      <div class="mb-6">
        <div class="d-flex flex-wrap align-center mb-4">
          <VAvatar
            :image="usflag"
            size="30"
            class="me-4"
          />

          <div>
            <div class="text-high-emphasis font-weight-medium">
              Domestic
            </div>
            <div>United state of America</div>
          </div>

          <VSpacer />

          <div class="text-disabled">
            <VBtn
              icon
              variant="text"
              color="default">
              <VIcon icon="tabler-edit" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              color="default">
              <VIcon icon="tabler-trash" />
            </VBtn>
          </div>
        </div>

        <VTable class="mb-4 border rounded">
          <thead>
            <tr>
              <th>RATE NAME</th>
              <th>CONDITION</th>
              <th>PRICE</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(data, index) in domesticTableData"
              :key="index"
            >
              <td>{{ data.rate }}</td>
              <td>{{ data.condition }}</td>
              <td>{{ data.price }}</td>
              <td>
                <VBtn
                  icon
                  variant="text"
                  color="default">
                  <VIcon icon="tabler-dots-vertical" />
                </VBtn>
              </td>
            </tr>
          </tbody>
        </VTable>

        <VBtn variant="tonal">
          Add rate
        </VBtn>
      </div>

      <div>
        <div class="d-flex flex-wrap align-center mb-4">
          <VIcon
            icon="tabler-world"
            size="30"
            class="me-4"
          />

          <div>
            <div class="text-high-emphasis">
              International
            </div>
            <div>United state of America</div>
          </div>

          <VSpacer />
          <div class="text-disabled">
            <VBtn
              icon
              variant="text"
              color="default">
              <VIcon icon="tabler-edit" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              color="default">
              <VIcon icon="tabler-trash" />
            </VBtn>
          </div>
        </div>

        <VTable class="mb-4 border rounded">
          <thead>
            <tr>
              <th>RATE NAME</th>
              <th>CONDITION</th>
              <th>PRICE</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(data, index) in InternationalTableData"
              :key="index"
            >
              <td>{{ data.rate }}</td>
              <td>{{ data.condition }}</td>
              <td>{{ data.price }}</td>
              <td>
                <VBtn
                  icon
                  variant="text"
                  color="default">
                  <VIcon icon="tabler-dots-vertical" />
                </VBtn>
              </td>
            </tr>
          </tbody>
        </VTable>

        <VBtn variant="tonal">
          Add rate
        </VBtn>
      </div>
    </VCardText>
  </VCard>

  <div class="d-flex justify-end gap-x-4">
    <VBtn
      variant="tonal"
      color="secondary"
    >
      Discard
    </VBtn>
    <VBtn>Save</VBtn>
  </div>
</template>
