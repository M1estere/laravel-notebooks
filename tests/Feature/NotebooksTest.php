<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotebooksTest extends TestCase
{
    const DEFAULT_TEST_DATA = [
        "fio"       => "Test name",
        "company"   => "Test company",
        "phone"     => "88888888",
        "email"     => "test_email@example.com",
        "birthdate" => "1990-10-10",
        "photo"     => "https://upload.wikimedia.org/wikipedia/commons/b/b6/Image_created_with_a_mobile_phone.png"
    ];

    const NOTEBOOK_DOESNOT_EXIST = 'Notebook not found';
    const NOTEBOOK_ALREADY_EXIST = 'Notebook with this fio, email or phone already exists';
    const INVALID_INPUT_DATA = 'Invalid input data';
    const DATABASE_CONNECTION_ERROR = 'Database connection error';

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    /**
     * Test success getting all notebooks
     * Checks response code and structure
     *
     * @return void
     */
    public function test_can_get_all_notebooks(): void
    {
        $response = $this->get('/api/v1/notebook');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                    "fio",
                    "company",
                    "phone",
                    "email",
                    "birthdate",
                    "photo",
                ],
            ],
            "first_page_url",
            "from",
            "last_page",
            "last_page_url",
            "links" => [
                '*' => [
                    "url",
                    "label",
                    "active",
                ],
            ],
            "next_page_url",
            "path",
            "per_page",
            "prev_page_url",
            "to",
            "total"
        ]);
    }

    /**
     * Test success notebook creation
     * Checks response code and presence in db
     *
     * @return void
     */
    public function test_can_create_notebook(): void
    {
        $response = $this->post('/api/v1/notebook', self::DEFAULT_TEST_DATA);

        $response->assertStatus(201);
        $this->assertDatabaseHas('notebooks', self::DEFAULT_TEST_DATA);
    }

    /**
     * Tests error notebook creation duplicate
     * Checks status code and response message
     *
     * @return void
     */
    public function test_error_create_duplicate_notebook(): void
    {
        $this->post('/api/v1/notebook', self::DEFAULT_TEST_DATA);
        $response = $this->post('/api/v1/notebook', self::DEFAULT_TEST_DATA);

        $response
            ->assertStatus(409)
            ->assertJson([
                "message" => self::NOTEBOOK_ALREADY_EXIST
            ]);
    }

    /**
     * Tests error notebook creation invalid data
     * Checks status code and response message
     *
     * @return void
     */
    public function test_error_create_invalid_data_notebook(): void
    {
        $data = [
            "fio"       => "Test name",
            "company"   => 2,
            "phone"     => 44,
            "email"     => true,
            "birthdate" => "1990-10-10",
            "photo"     => "test_url"
        ];

        $response = $this->post('/api/v1/notebook', $data);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => self::INVALID_INPUT_DATA
            ]);
    }

    /**
     * Tests success getting notebook
     * Checks status code and response data
     *
     * @return void
     */
    public function test_can_get_notebook(): void
    {
        $notebook = \App\Models\Notebook::factory()->create();

        $response = $this->get('/api/v1/notebook/' . $notebook->id . '/');

        $response->assertStatus(200);
        $response->assertJson([
            "id"        => $notebook->id,
            "fio"       => $notebook->fio,
            "company"   => $notebook->company,
            "phone"     => $notebook->phone,
            "email"     => $notebook->email,
            "birthdate" => $notebook->birthdate,
            "photo"     => $notebook->photo
        ]);
    }

    /**
     * Tests error getting notebook doesnot exist
     * Checks status code and response data
     *
     * @return void
     */
    public function test_error_get_notebook_doesnot_exist(): void
    {
        $response = $this->get('/api/v1/notebook/' . -1 . '/');

        $response
            ->assertStatus(404)
            ->assertJson([
                "message" => self::NOTEBOOK_DOESNOT_EXIST
            ]);
    }

    /**
     * Tests success updating notebook
     * Checks status code and presence in db
     *
     * @return void
     */
    public function test_can_update_notebook(): void
    {
        $notebook = \App\Models\Notebook::factory()->create();

        $response = $this->put('/api/v1/notebook/' . $notebook->id, self::DEFAULT_TEST_DATA);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notebooks', self::DEFAULT_TEST_DATA);
    }

    /**
     * Tests error updating notebook invalid data
     * Checks status code and response message
     *
     * @return void
     */
    public function test_error_update_notebook_invalid_data(): void
    {
        $data = [
            "fio"       => "Test name",
            "company"   => 2,
            "phone"     => 44,
            "email"     => true,
            "birthdate" => "1990-10-10",
            "photo"     => "https://upload.wikimedia.org/wikipedia/commons/b/b6/Image_created_with_a_mobile_phone.png"
        ];

        $response = $this->post('/api/v1/notebook', $data);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => self::INVALID_INPUT_DATA
            ]);
    }

    /**
     * Tests error updating notebook doesnot exist
     * Checks status code and absence in db
     *
     * @return void
     */
    public function test_error_update_notebook_doesnot_exist(): void
    {
        $response = $this->put('/api/v1/notebook/' . -1, self::DEFAULT_TEST_DATA);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('notebooks', self::DEFAULT_TEST_DATA);
    }

    /**
     * Tests success deleting notebook
     * Checks status code and absence in db
     *
     * @return void
     */
    public function test_can_delete_notebook(): void
    {
        $notebook = \App\Models\Notebook::factory()->create();

        $response = $this->delete('/api/v1/notebook/' . $notebook->id . '/');

        $response->assertStatus(204);
        $this->assertDatabaseMissing('notebooks', ['id' => $notebook->id]);
    }

    /**
     * Tests error deleting notebook doesnot exist
     * Checks status code
     *
     * @return void
     */
    public function test_error_delete_notebook_doesnot_exist(): void
    {
        $response = $this->delete('/api/v1/notebook/' . -1 . '/');

        $response->assertStatus(404);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }
}
