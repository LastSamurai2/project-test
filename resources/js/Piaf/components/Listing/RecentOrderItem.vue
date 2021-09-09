<template>
<div class="d-flex flex-row mb-3">
    <inertia-link tag="a" :href="route('dashboard')" class="d-block position-relative">
        <img :src="order.img" :alt="order.title" class="list-thumbnail border-0" />
        <b-badge :variant="order.statusColor" pill class="position-absolute badge-top-right">{{order.status}}</b-badge>
    </inertia-link>
    <div class="pl-3 pt-2 pr-2 pb-2">
        <inertia-link tag="a" :href="route('dashboard')">
            <p class="list-item-heading">{{ order.title }}</p>
            <div class="pr-4">
                <p class="text-muted mb-1 text-small">{{ order.description }}</p>
            </div>
            <div class="text-primary text-small font-weight-medium d-none d-sm-block">{{ order.createDate }}</div>
        </inertia-link>
    </div>
</div>
</template>

<script>
export default {
    props: ['order', 'detailPath']
}
</script>
