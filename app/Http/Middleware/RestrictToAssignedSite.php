namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToAssignedSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Allow admin to access all sites
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // Check if the user is assigned to a site
        if ($user && $user->site) {
            $siteId = $request->route('site');

            // Ensure the user can only access their assigned site
            if ($siteId && $siteId != $user->site->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return $next($request);
    }
}