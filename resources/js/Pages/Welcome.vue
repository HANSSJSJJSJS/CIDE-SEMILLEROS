<template>
    <div>
        <h1>Bienvenido a mi aplicación personalizada</h1>
        <p>Laravel versión: {{ laravelVersion }}</p>
        <p>PHP versión: {{ phpVersion }}</p>
    </div>
</template>

<script>
export default {
    props: {
        laravelVersion: String,
        phpVersion: String,
        canLogin: Boolean,
        canRegister: Boolean
    }
}
</script>
