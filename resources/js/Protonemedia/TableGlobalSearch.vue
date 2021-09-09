<template>
    <div class="d-block d-md-inline-block">
        <input
            class="form-control"
            style="padding: 0.58rem 0.75rem; border-radius: 5px"
            :placeholder="$t('table.search')"
            :value="value"
            type="text"
            @input="onChange($event.target.value)"
        />
    </div>
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [Components.TableGlobalSearch],
};
</script>
