<?php

namespace Tests\Feature;

use App\Models\Keyword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class KeywordControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_keywords_ordered_by_name()
    {
        $keywordA = Keyword::factory()->create(['name' => 'aaa']);
        $keywordB = Keyword::factory()->create(['name' => 'bbb']);

        $response = $this->getJson('/api/keywords');

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.name', 'aaa')
                 ->assertJsonPath('data.1.name', 'bbb');
    }

    #[Test]
    public function can_create_keyword()
    {
        $data = ['name' => 'NewKeyword'];

        $response = $this->postJson('/api/keywords', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'NewKeyword']);

        $this->assertDatabaseHas('keywords', ['name' => 'NewKeyword']);
    }

    #[Test]
    public function cannot_create_keyword_with_duplicate_name()
    {
        $keyword = Keyword::factory()->create(['name' => 'Duplicate']);

        $response = $this->postJson('/api/keywords', ['name' => 'Duplicate']);

        $response->assertStatus(422); // Validación falla
    }

    #[Test]
    public function can_update_keyword()
    {
        $keyword = Keyword::factory()->create(['name' => 'OldName']);

        $response = $this->putJson("/api/keywords/{$keyword->id}", ['name' => 'UpdatedName']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'UpdatedName']);

        $this->assertDatabaseHas('keywords', ['id' => $keyword->id, 'name' => 'UpdatedName']);
    }

    #[Test]
    public function can_delete_keyword()
    {
        $keyword = Keyword::factory()->create();

        $response = $this->deleteJson("/api/keywords/{$keyword->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Keyword deleted successfully']);

        $this->assertDatabaseMissing('keywords', ['id' => $keyword->id]);
    }
}
