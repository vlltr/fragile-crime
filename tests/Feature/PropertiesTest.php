<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_owner_has_access_to_properties_feature(): void
    {
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        $response = $this->actingAs($owner)->getJson('/api/owner/properties');

        $response->assertStatus(200);
    }

    public function test_user_does_not_have_access_to_properties_feature(): void
    {
        $owner = User::factory()->create(['role_id' => Role::ROLE_USER]);
        $response = $this->actingAs($owner)->getJson('/api/owner/properties');

        $response->assertStatus(403);
    }

    public function test_property_owner_can_create_property(): void
    {
        $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
        $response = $this->actingAs($owner)->postJson('/api/owner/properties', [
            'name' => 'Property 1',
            'city_id' => City::value('id'),
            'address_street' => 'Street 1',
            'address_postcode' => '1234',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment([
            'name' => 'Property 1',
        ]);
    }

    public function test_property_search_beds_list_all_cases(): void
{
    $owner = User::factory()->create(['role_id' => Role::ROLE_OWNER]);
    $cityId = City::value('id');
    $roomTypes = RoomType::all();
    $bedTypes = BedType::all();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'city_id' => $cityId,
    ]);
    $apartment = Apartment::factory()->create([
        'name' => 'Small apartment',
        'property_id' => $property->id,
        'capacity_adults' => 1,
        'capacity_children' => 0,
    ]);

    // ----------------------
    // FIRST: check that bed list if empty if no beds
    // ----------------------

    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonCount(1);
    $response->assertJsonCount(1, '0.apartments');
    $response->assertJsonPath('0.apartments.0.beds_list', '');

    // ----------------------
    // SECOND: create 1 room with 1 bed
    // ----------------------

    $room = Room::create([
        'apartment_id' => $apartment->id,
        'room_type_id' => $roomTypes[0]->id,
        'name' => 'Bedroom',
    ]);
    Bed::create([
        'room_id' => $room->id,
        'bed_type_id' => $bedTypes[0]->id,
    ]);

    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '1 ' . $bedTypes[0]->name);

    // ----------------------
    // THIRD: add another bed to the same room
    // ----------------------

    Bed::create([
        'room_id' => $room->id,
        'bed_type_id' => $bedTypes[0]->id,
    ]);
    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '2 ' . str($bedTypes[0]->name)->plural());

    // ----------------------
    // FOURTH: add a second room with no beds
    // ----------------------

    $secondRoom = Room::create([
        'apartment_id' => $apartment->id,
        'room_type_id' => $roomTypes[0]->id,
        'name' => 'Living room',
    ]);
    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '2 ' . str($bedTypes[0]->name)->plural());

    // ----------------------
    // FIFTH: add one bed to that second room
    // ----------------------

    Bed::create([
        'room_id' => $secondRoom->id,
        'bed_type_id' => $bedTypes[0]->id,
    ]);
    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '3 ' . str($bedTypes[0]->name)->plural());

    // ----------------------
    // SIXTH: add another bed with a different type to that second room
    // ----------------------

    Bed::create([
        'room_id' => $secondRoom->id,
        'bed_type_id' => $bedTypes[1]->id,
    ]);
    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '4 beds (3 ' . str($bedTypes[0]->name)->plural() . ', 1 ' . $bedTypes[1]->name . ')');

    // ----------------------
    // SEVENTH: add a second bed with that new type to that second room
    // ----------------------

    Bed::create([
        'room_id' => $secondRoom->id,
        'bed_type_id' => $bedTypes[1]->id,
    ]);
    $response = $this->getJson('/api/search?city=' . $cityId);
    $response->assertStatus(200);
    $response->assertJsonPath('0.apartments.0.beds_list', '5 beds (3 ' . str($bedTypes[0]->name)->plural() . ', 2 ' . str($bedTypes[1]->name)->plural() . ')');
}
}
