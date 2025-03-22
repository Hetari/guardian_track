<template>
  <div class="relative z-[999999] flex size-full w-full flex-col justify-end">
    <div class="flex h-fit flex-col justify-evenly pt-12">
      <!-- First Row -->
      <div class="relative">
        <div class="container mx-auto flex items-center justify-between px-12 py-10 text-white">
          <p>Intrusion Detection</p>
          <p>Server Integrity</p>
          <p>End-to-End Encryption</p>
        </div>

        <!-- Horizontal Line -->
        <div class="absolute left-0 right-0 top-0 h-[2px] bg-white opacity-30"></div>
      </div>
      <div class="relative">
        <div class="container mx-auto flex items-center justify-between px-12 py-10 text-white">
          <p>Login</p>
          <p>Access Control</p>
        </div>

        <!-- Horizontal Line -->
        <div class="absolute left-0 right-0 top-0 h-[2px] bg-white opacity-30"></div>
      </div>
    </div>
  </div>

  <!-- circles -->
  <div class="absolute -bottom-1/4 left-1/2 h-[90vh] w-[90vw] -translate-x-1/2 translate-y-1/3 bg-[#4FBBB9] opacity-30 blur-[200px]" />
  <div class="absolute bottom-0 left-1/2 h-[50vh] w-[50vw] -translate-x-1/2 translate-y-1/2 bg-[#4FBBB9] opacity-50 blur-[150px]" />
  <div class="absolute bottom-0 left-1/2 size-[30vw] -translate-x-1/2 translate-y-1/2 rounded-full bg-[#4FBBB9] opacity-100 blur-[100px]" />
</template>
