<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(title="Comment Auto-Reply API", version="1.0.0")
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT"
 * )
 * @OA\Tag(name="Pages", description="Facebook pages management")
 * @OA\Tag(name="Posts", description="Posts import & toggle")
 * @OA\Tag(name="Templates", description="Templates per post")
 * @OA\Tag(name="Subscriptions", description="Plans & quotas")
 * @OA\Tag(name="Logs", description="Delivery logs")
 */
abstract class Controller
{
    //
}
