<?php

namespace Renatio\DynamicPDF\Tests\Controllers;

use Renatio\DynamicPDF\Models\Layout;

class LayoutsTest extends ControllerTestCase
{

    /** @test */
    public function it_displays_index_layout_page()
    {
        $layout = factory(Layout::class)->create();

        $this->get($this->backendUri.'/renatio/dynamicpdf/templates/index/layouts')
            ->assertSee($layout->name)
            ->assertSuccessful();
    }

    /** @test */
    public function it_displays_create_layout_page()
    {
        $this->get($this->backendUri.'/renatio/dynamicpdf/layouts/create')
            ->assertSuccessful();
    }

    /** @test */
    public function it_displays_update_layout_page()
    {
        $layout = factory(Layout::class)->create();

        $this->get($this->backendUri.'/renatio/dynamicpdf/layouts/update/'.$layout->id)
            ->assertSuccessful();
    }

    /** @test */
    public function it_displays_preview_pdf_layout_page()
    {
        $layout = factory(Layout::class)->create();

        $this->get($this->backendUri.'/renatio/dynamicpdf/layouts/previewpdf/'.$layout->id)
            ->assertHeader('content-type', 'application/pdf')
            ->assertSuccessful();
    }

    /** @test */
    public function it_displays_preview_html_layout_page()
    {
        $layout = factory(Layout::class)->create();

        $this->get($this->backendUri.'/renatio/dynamicpdf/layouts/preview/'.$layout->id)
            ->assertSuccessful();
    }
}
