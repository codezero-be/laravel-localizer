<?php

namespace CodeZero\Localizer\Middleware;

use CodeZero\Localizer\Localizer;
use Closure;

class SetLocale
{
    /**
     * Localizer.
     *
     * @var \CodeZero\Localizer\Localizer
     */
    protected $localizer;

    /**
     * Create a new SetLocale instance.
     *
     * @param \CodeZero\Localizer\Localizer $localizer
     */
    public function __construct(Localizer $localizer)
    {
        $this->localizer = $localizer;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $this->localizer->detect();

        if ($locale) {
            $this->localizer->store($locale);
        }

        return $next($request);
    }
}
