<div class="text-white">
    <h3 class="text-2xl font-bold mb-4">Klant informatie</h3>
    <div class="mb-4">
        <label for="name" class="block text-sm">Naam</label>
        <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="name" id="name" required>
    </div>
    <div class="mb-4">
        <label for="email" class="block text-sm">Email</label>
        <input type="email" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="email" id="email" required>
    </div>
    <h3 class="text-xl mt-4">Aflever adres</h3>
    <div class="mb-4">
        <label for="postalcode" class="block text-sm">Postcode</label>
        <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="postalcode" id="postalcode" pattern="\d{4} [A-Za-z]{2}" title="Voer een geldige postcode in (bijv, 8302 BB)" required>
    </div>
    <div class="mb-4">
        <label for="housenr" class="block text-sm">Huisnummer</label>
        <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="housenr" id="housenr" pattern="\d{1,3}\s?[A-Za-z]{0,1}" title="Voer een geldig huis nummer in (bijv, 24 B)" required>
    </div>
    <div class="mb-4">
        <label for="residence" class="block text-sm">Woonplaats</label>
        <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="residence" id="residence" required>
    </div>
</div>