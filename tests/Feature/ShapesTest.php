<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Passport;
use App\User;

class ShapesTest extends TestCase
{
    /**
     * Get array of random coordinates with given length
     *
     * @param int $arrayLength
     * @return array
     */
    private function generateLatLngArray(int $arrayLength)
    {
        $arrayOut = [];
        for ($i = 0; $i < $arrayLength; $i++) {
            $lat = (float)rand(-100, 100);
            $lng = (float)rand(-100, 100);
            
            $arrayOut[] = [
                'lat' => $lat / 10,
                'lng' => $lng / 10,
            ];
        }
        return $arrayOut;
    }

    /**
     * Checks if coordinate storing request allows only more 3 points to save
     */
    public function testValidShapePointCounts()
    {
        Passport::actingAs(
            factory(User::class)->create(),
            ['create-servers']
        );

        $response = $this->json('POST', '/api/shapes', [
            'body' => [
                'coordinates' => $this->generateLatLngArray(2),
            ]
        ]);

        $response->assertStatus(422);
        
        $response = $this->json('POST', '/api/shapes', [
            'body' => [
                'coordinates' => $this->generateLatLngArray(rand(3,50)),
            ]
        ]);

        $response->assertStatus(200);
    }
}
