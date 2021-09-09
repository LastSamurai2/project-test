<template>
  <div>
    <p v-if="pagination.total < 1">{{ translations.no_results_found }}</p>
    <nav v-if="pagination.total > 0" class="mt-4">
      <ul class="pagination justify-content-center pagination-sm">
        <li v-bind:class="{'page-item':true, 'disabled':(!previousPageUrl)}">
          <component
              :is="previousPageUrl ? 'inertia-link' : 'div'"
              :href="previousPageUrl"
              class="page-link"
          ><span>
              <i class="simple-icon-arrow-left"></i></span></component></li>
        <template v-for="(link, key) in pagination.links">
          <li :class="{'page-item': true, 'active': link.active}">
            <component
                v-if="!isNaN(link.label) || link.label === '...'"
                :is="link.url ? 'inertia-link' : 'div'"
                :href="link.url"
                class="page-link"
                :class="{'hover:bg-gray-50': link.url, 'bg-gray-100': link.active}"
            >{{ link.label }}</component>
          </li>
        </template>
        <li v-bind:class="{'page-item':true, 'disabled':(!nextPageUrl)}">
          <component
              :is="nextPageUrl ? 'inertia-link' : 'div'"
              :href="nextPageUrl"
              class="page-link"
          >
            <span>
              <i class="simple-icon-arrow-right"></i></span></component></li>
      </ul>
    </nav>
    <div
        v-if="pagination.total > 0">

      <p class="text-right">
        <span class="font-medium">{{ pagination.from }}</span>
        {{ $t('table.to') }}
        <span class="font-medium">{{ pagination.to }}</span>
        {{ $t('table.of') }}
        <span class="font-medium">{{ pagination.total }}</span>
        {{ $t('table.results') }}
      </p>
    </div>

  </div>
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [Components.Pagination],
};
</script>
