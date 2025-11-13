<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div 
    x-show="loginModal"
    class="fixed inset-0 z-50 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
>
    <div @click.away="loginModal = false" class="relative mx-auto p-8 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900">Login</h3>
            <div class="mt-4 px-7 py-3">
                <form action="<?= base_url('index.php/home/login') ?>" method="POST">
                    <input class="w-full px-4 py-2 mt-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-green-600"
                        type="email" placeholder="Email" name="email" required>
                    <input class="w-full px-4 py-2 mt-4 border rounded-md focus:outline-none focus:ring-1 focus:ring-green-600"
                        type="password" placeholder="Password" name="password" required>
                    
                    <div class="flex items-center justify-center mt-6">
                        <button type="submit"
                            class="px-6 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 w-full">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div 
    x-show="registerModal"
    class="fixed inset-0 z-50 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;">
    
    <div @click.away="registerModal = false" class="relative mx-auto p-8 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900">Register as <span x-text="registerRole === 'user' ? 'User' : 'Agent'"></span></h3>
            <div class="mt-4 px-7 py-3">
                <form action="<?= site_url('home/register') ?>" method="POST">
                    <input type="hidden" name="role" x-model="registerRole">
                    
                    <input class="w-full px-4 py-2 mt-2 border rounded-md" type="text" placeholder="Full Name" name="name" required>
                    <input class="w-full px-4 py-2 mt-4 border rounded-md" type="email" placeholder="Email" name="email" required>
                    <input class="w-full px-4 py-2 mt-4 border rounded-md" type="password" placeholder="Password" name="password" required>
                    <input class="w-full px-4 py-2 mt-4 border rounded-md" type="text" placeholder="Phone Number" name="phone" required>
                    
                    <div x-show="registerRole === 'agent'">
                            <input list="wilayah-list" 
                            type="text" 
                            name="wilayah" 
                            id="reg-wilayah" 
                            :required="registerRole === 'agent'"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" 
                            placeholder="Ketik atau pilih wilayah...">
                                                    
                        <datalist id="wilayah-list">
                            <?php 
                            // Variabel $list_wilayah ini didapat dari Home.php
                            if (!empty($list_wilayah)): 
                                foreach($list_wilayah as $wilayah): 
                            ?>
                                <option value="<?= htmlspecialchars($wilayah['nama_wilayah']); ?>">
                            <?php 
                                endforeach; 
                            endif; 
                            ?>
                        </datalist>
                    </div>

                    <div class="flex items-center justify-center mt-6">
                        <button type="submit" class="px-6 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 w-full">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
