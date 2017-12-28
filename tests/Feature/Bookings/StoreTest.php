<?php

namespace Tests\Feature\Bookings;

use Hydrofon\Booking;
use Hydrofon\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bookings can be created and stored.
     *
     * @return void
     */
    public function testBookingsCanBeStored()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);
    }

    /**
     * Bookings is owned by current user.
     *
     * @return void
     */
    public function testBookingsCanBeStoredWithoutUserID()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'user_id'    => '',
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'user_id'   => $user->id,
            'object_id' => $booking->object_id,
        ]);
    }

    /**
     * Bookings must have an object.
     *
     * @return void
     */
    public function testBookingsMustHaveAnObject()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('categories', [
            'object_id'  => '',
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * The requested object must exist in the database.
     *
     * @return void
     */
    public function testObjectMustExist()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => 100,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * Bookings must have a start time.
     *
     * @return void
     */
    public function testBookingsMustHaveAStartTime()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => '',
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * Start time must be a valid timestamp.
     *
     * @return void
     */
    public function testStartTimeMustBeValidTimestamp()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => 'not-valid-time',
            'end_time'   => $booking->end_time,
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * Bookings must have a end time.
     *
     * @return void
     */
    public function testBookingsMustHaveAEndTime()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => '',
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * End time must be a valid timestamp.
     *
     * @return void
     */
    public function testEndTimeMustBeValidTimestamp()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => 'not-valid-time',
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }

    /**
     * Booking have to start before it ends.
     *
     * @return void
     */
    public function testStartTimeMustBeBeforeEndTime()
    {
        $user    = factory(User::class)->create();
        $booking = factory(Booking::class)->make();

        $response = $this->actingAs($user)->post('bookings', [
            'object_id'  => $booking->object_id,
            'start_time' => $booking->start_time,
            'end_time'   => $booking->start_time->copy()->subHour(),
        ]);

        $response->assertRedirect();
        $this->assertCount(0, Booking::all());
    }
}