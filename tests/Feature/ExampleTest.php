<?php

it('returns a successful response', function () {
    $response = $this->get(route('index', absolute: false));

    $response->assertOk();
});
