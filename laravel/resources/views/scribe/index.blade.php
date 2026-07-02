<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>PESAT API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .python-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;python&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="python">python</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-edge-cameras">
                                <a href="#endpoints-GETapi-edge-cameras">GET api/edge/cameras</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-edge-heartbeat">
                                <a href="#endpoints-POSTapi-edge-heartbeat">POST api/edge/heartbeat</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-kamera-cctv" class="tocify-header">
                <li class="tocify-item level-1" data-unique="kamera-cctv">
                    <a href="#kamera-cctv">Kamera CCTV</a>
                </li>
                                    <ul id="tocify-subheader-kamera-cctv" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="kamera-cctv-GETapi-cameras--id-">
                                <a href="#kamera-cctv-GETapi-cameras--id-">Ambil Detail Kamera</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-laporan-warga" class="tocify-header">
                <li class="tocify-item level-1" data-unique="laporan-warga">
                    <a href="#laporan-warga">Laporan Warga</a>
                </li>
                                    <ul id="tocify-subheader-laporan-warga" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="laporan-warga-POSTapi-reports">
                                <a href="#laporan-warga-POSTapi-reports">Kirim Laporan Warga / AI Detection Report</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="laporan-warga-GETapi-reports-latest">
                                <a href="#laporan-warga-GETapi-reports-latest">Ambil Laporan Terbaru (Polling)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="laporan-warga-GETapi-wh-reports">
                                <a href="#laporan-warga-GETapi-wh-reports">Ambil Laporan Pending untuk WH Officer</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="laporan-warga-POSTapi-wh-reports--id--verify">
                                <a href="#laporan-warga-POSTapi-wh-reports--id--verify">Verifikasi Laporan Warga</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-pengaturan-admin" class="tocify-header">
                <li class="tocify-item level-1" data-unique="pengaturan-admin">
                    <a href="#pengaturan-admin">Pengaturan Admin</a>
                </li>
                                    <ul id="tocify-subheader-pengaturan-admin" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="pengaturan-admin-GETapi-admin-settings">
                                <a href="#pengaturan-admin-GETapi-admin-settings">Ambil Semua Pengaturan Admin</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="pengaturan-admin-POSTapi-admin-settings">
                                <a href="#pengaturan-admin-POSTapi-admin-settings">Update Pengaturan Admin</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-telemetri-ai" class="tocify-header">
                <li class="tocify-item level-1" data-unique="telemetri-ai">
                    <a href="#telemetri-ai">Telemetri AI</a>
                </li>
                                    <ul id="tocify-subheader-telemetri-ai" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="telemetri-ai-POSTapi-telemetry-log">
                                <a href="#telemetri-ai-POSTapi-telemetry-log">Kirim Log Telemetri Deteksi</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="telemetri-ai-GETapi-telemetry-latest">
                                <a href="#telemetri-ai-GETapi-telemetry-latest">Ambil Log Deteksi Terbaru (Polling)</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: June 26, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Dokumentasi API untuk Sistem Informasi Pengawasan Smart City (PESAT) - GEMASTIK 2026</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>Dokumentasi ini menyediakan referensi untuk berintegrasi dengan API PESAT.
Sistem ini mencakup endpoint untuk pengiriman telemetri AI dari edge device, laporan warga, serta pengaturan sistem.</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_AUTH_KEY}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Gunakan Bearer Token yang didapatkan dari konfigurasi edge device.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-edge-cameras">GET api/edge/cameras</h2>

<p>
</p>



<span id="example-requests-GETapi-edge-cameras">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/edge/cameras" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"device_id\": \"b\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/edge/cameras"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "device_id": "b"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/edge/cameras'
payload = {
    "device_id": "b"
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-edge-cameras">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
x-ratelimit-limit: 60
x-ratelimit-remaining: 56
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: []
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-edge-cameras" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-edge-cameras"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-edge-cameras"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-edge-cameras" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-edge-cameras">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-edge-cameras" data-method="GET"
      data-path="api/edge/cameras"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-edge-cameras', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-edge-cameras"
                    onclick="tryItOut('GETapi-edge-cameras');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-edge-cameras"
                    onclick="cancelTryOut('GETapi-edge-cameras');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-edge-cameras"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/edge/cameras</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-edge-cameras"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-edge-cameras"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="GETapi-edge-cameras"
               value="b"
               data-component="body">
    <br>
<p>validation.max. Example: <code>b</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-edge-heartbeat">POST api/edge/heartbeat</h2>

<p>
</p>



<span id="example-requests-POSTapi-edge-heartbeat">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/edge/heartbeat" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"device_id\": \"b\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/edge/heartbeat"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "device_id": "b"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/edge/heartbeat'
payload = {
    "device_id": "b"
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-edge-heartbeat">
</span>
<span id="execution-results-POSTapi-edge-heartbeat" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-edge-heartbeat"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-edge-heartbeat"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-edge-heartbeat" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-edge-heartbeat">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-edge-heartbeat" data-method="POST"
      data-path="api/edge/heartbeat"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-edge-heartbeat', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-edge-heartbeat"
                    onclick="tryItOut('POSTapi-edge-heartbeat');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-edge-heartbeat"
                    onclick="cancelTryOut('POSTapi-edge-heartbeat');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-edge-heartbeat"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/edge/heartbeat</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-edge-heartbeat"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-edge-heartbeat"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTapi-edge-heartbeat"
               value="b"
               data-component="body">
    <br>
<p>validation.max. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metrics</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="metrics"                data-endpoint="POSTapi-edge-heartbeat"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                <h1 id="kamera-cctv">Kamera CCTV</h1>

    

                                <h2 id="kamera-cctv-GETapi-cameras--id-">Ambil Detail Kamera</h2>

<p>
</p>



<span id="example-requests-GETapi-cameras--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/cameras/CAM-001" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/cameras/CAM-001"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/cameras/CAM-001'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-cameras--id-">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: &quot;CAM-001&quot;,
    &quot;location_name&quot;: &quot;Taman Riyadhah&quot;,
    &quot;latitude&quot;: &quot;5.18020000&quot;,
    &quot;longitude&quot;: &quot;97.15070000&quot;,
    &quot;is_active&quot;: true
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Tidak Ditemukan):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Camera not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-cameras--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-cameras--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-cameras--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-cameras--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-cameras--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-cameras--id-" data-method="GET"
      data-path="api/cameras/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-cameras--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-cameras--id-"
                    onclick="tryItOut('GETapi-cameras--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-cameras--id-"
                    onclick="cancelTryOut('GETapi-cameras--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-cameras--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/cameras/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-cameras--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-cameras--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-cameras--id-"
               value="CAM-001"
               data-component="url">
    <br>
<p>ID kamera. Example: <code>CAM-001</code></p>
            </div>
                    </form>

                <h1 id="laporan-warga">Laporan Warga</h1>

    

                                <h2 id="laporan-warga-POSTapi-reports">Kirim Laporan Warga / AI Detection Report</h2>

<p>
</p>

<p>Endpoint untuk menerima laporan dari sistem AI edge device atau warga.
Sistem otomatis mendeteksi mode jam istirahat (break mode) dan menandai laporan sebagai prioritas.
Jika laporan pending dengan lokasi yang sama sudah ada, akan di-update (upsert).</p>

<span id="example-requests-POSTapi-reports">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/reports" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "location_name=Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab"\
    --form "latitude=5.1802"\
    --form "longitude=97.1507"\
    --form "media=@C:\Users\DELL\AppData\Local\Temp\php78FB.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reports"
);

const headers = {
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('location_name', 'Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab');
body.append('latitude', '5.1802');
body.append('longitude', '97.1507');
body.append('media', document.querySelector('input[name="media"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/reports'
files = {
  'location_name': (None, 'Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab'),
  'latitude': (None, '5.1802'),
  'longitude': (None, '97.1507'),
  'media': open('C:\Users\DELL\AppData\Local\Temp\php78FB.tmp', 'rb')}
payload = {
    "location_name": "Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab",
    "latitude": 5.1802,
    "longitude": 97.1507
}
headers = {
  'Content-Type': 'multipart/form-data',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, files=files)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-reports">
            <blockquote>
            <p>Example response (201, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: &quot;01J5X...&quot;,
        &quot;location_name&quot;: &quot;Taman Riyadhah&quot;,
        &quot;status&quot;: &quot;pending&quot;,
        &quot;is_break_dispatch&quot;: false
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-reports" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-reports"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-reports"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-reports" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-reports">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-reports" data-method="POST"
      data-path="api/reports"
      data-authed="0"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-reports', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-reports"
                    onclick="tryItOut('POSTapi-reports');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-reports"
                    onclick="cancelTryOut('POSTapi-reports');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-reports"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/reports</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-reports"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-reports"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>location_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="location_name"                data-endpoint="POSTapi-reports"
               value="Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab"
               data-component="body">
    <br>
<p>Nama lokasi kejadian. Example: <code>Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>latitude</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="latitude"                data-endpoint="POSTapi-reports"
               value="5.1802"
               data-component="body">
    <br>
<p>Koordinat latitude GPS. Example: <code>5.1802</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>longitude</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="longitude"                data-endpoint="POSTapi-reports"
               value="97.1507"
               data-component="body">
    <br>
<p>Koordinat longitude GPS. Example: <code>97.1507</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>media</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="media"                data-endpoint="POSTapi-reports"
               value=""
               data-component="body">
    <br>
<p>Bukti media (jpg/png/mp4/mov/avi, max 20MB). Example: <code>C:\Users\DELL\AppData\Local\Temp\php78FB.tmp</code></p>
        </div>
        </form>

                    <h2 id="laporan-warga-GETapi-reports-latest">Ambil Laporan Terbaru (Polling)</h2>

<p>
</p>

<p>Endpoint polling untuk mendapatkan laporan terbaru secara incremental.</p>

<span id="example-requests-GETapi-reports-latest">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reports/latest?after_id=01J5XABC123&amp;status=pending" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"after_id\": \"architecto\",
    \"status\": \"verified\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reports/latest"
);

const params = {
    "after_id": "01J5XABC123",
    "status": "pending",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "after_id": "architecto",
    "status": "verified"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/reports/latest'
payload = {
    "after_id": "architecto",
    "status": "verified"
}
params = {
  'after_id': '01J5XABC123',
  'status': 'pending',
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, json=payload, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-reports-latest">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;pending&quot;: [],
        &quot;history&quot;: []
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reports-latest" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reports-latest"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reports-latest"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reports-latest" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reports-latest">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reports-latest" data-method="GET"
      data-path="api/reports/latest"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reports-latest', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reports-latest"
                    onclick="tryItOut('GETapi-reports-latest');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reports-latest"
                    onclick="cancelTryOut('GETapi-reports-latest');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reports-latest"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reports/latest</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reports-latest"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reports-latest"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="after_id"                data-endpoint="GETapi-reports-latest"
               value="01J5XABC123"
               data-component="query">
    <br>
<p>ULID laporan terakhir. Hanya laporan lebih baru akan dikembalikan. Example: <code>01J5XABC123</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-reports-latest"
               value="pending"
               data-component="query">
    <br>
<p>Filter status: pending, verified, rejected. Example: <code>pending</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>after_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="after_id"                data-endpoint="GETapi-reports-latest"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-reports-latest"
               value="verified"
               data-component="body">
    <br>
<p>Example: <code>verified</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>verified</code></li> <li><code>rejected</code></li></ul>
        </div>
        </form>

                    <h2 id="laporan-warga-GETapi-wh-reports">Ambil Laporan Pending untuk WH Officer</h2>

<p>
</p>

<p>Mengembalikan semua laporan warga dengan status pending, diurutkan dari terbaru.</p>

<span id="example-requests-GETapi-wh-reports">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/wh/reports" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/wh/reports"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/wh/reports'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-wh-reports">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: &quot;01J5X...&quot;,
            &quot;location_name&quot;: &quot;Taman Riyadhah&quot;,
            &quot;status&quot;: &quot;pending&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-wh-reports" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-wh-reports"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wh-reports"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-wh-reports" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wh-reports">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-wh-reports" data-method="GET"
      data-path="api/wh/reports"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-wh-reports', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-wh-reports"
                    onclick="tryItOut('GETapi-wh-reports');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-wh-reports"
                    onclick="cancelTryOut('GETapi-wh-reports');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-wh-reports"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/wh/reports</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-wh-reports"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-wh-reports"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="laporan-warga-POSTapi-wh-reports--id--verify">Verifikasi Laporan Warga</h2>

<p>
</p>

<p>WH Officer memverifikasi (menerima/menolak) laporan warga.</p>

<span id="example-requests-POSTapi-wh-reports--id--verify">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/wh/reports/01J5XABC123DEF456/verify" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"status\": \"verified\",
    \"verification_notes\": \"Tindakan selesai dilakukan di lapangan.\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/wh/reports/01J5XABC123DEF456/verify"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "status": "verified",
    "verification_notes": "Tindakan selesai dilakukan di lapangan."
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/wh/reports/01J5XABC123DEF456/verify'
payload = {
    "status": "verified",
    "verification_notes": "Tindakan selesai dilakukan di lapangan."
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-wh-reports--id--verify">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: &quot;01J5X...&quot;,
        &quot;status&quot;: &quot;verified&quot;,
        &quot;verified_by&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-wh-reports--id--verify" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-wh-reports--id--verify"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wh-reports--id--verify"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-wh-reports--id--verify" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wh-reports--id--verify">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-wh-reports--id--verify" data-method="POST"
      data-path="api/wh/reports/{id}/verify"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-wh-reports--id--verify', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-wh-reports--id--verify"
                    onclick="tryItOut('POSTapi-wh-reports--id--verify');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-wh-reports--id--verify"
                    onclick="cancelTryOut('POSTapi-wh-reports--id--verify');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-wh-reports--id--verify"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/wh/reports/{id}/verify</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-wh-reports--id--verify"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-wh-reports--id--verify"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-wh-reports--id--verify"
               value="01J5XABC123DEF456"
               data-component="url">
    <br>
<p>ULID laporan. Example: <code>01J5XABC123DEF456</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-wh-reports--id--verify"
               value="verified"
               data-component="body">
    <br>
<p>Status verifikasi: verified atau rejected. Example: <code>verified</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>verification_notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="verification_notes"                data-endpoint="POSTapi-wh-reports--id--verify"
               value="Tindakan selesai dilakukan di lapangan."
               data-component="body">
    <br>
<p>Catatan tindakan lapangan. Example: <code>Tindakan selesai dilakukan di lapangan.</code></p>
        </div>
        </form>

                <h1 id="pengaturan-admin">Pengaturan Admin</h1>

    

                                <h2 id="pengaturan-admin-GETapi-admin-settings">Ambil Semua Pengaturan Admin</h2>

<p>
</p>

<p>Mengembalikan semua pengaturan admin dalam format key-value.</p>

<span id="example-requests-GETapi-admin-settings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/settings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/settings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/admin/settings'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-settings">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;break_mode_active&quot;: &quot;false&quot;,
        &quot;break_start_time&quot;: &quot;12:00&quot;,
        &quot;break_end_time&quot;: &quot;14:00&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-settings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-settings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-settings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-settings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-settings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-settings" data-method="GET"
      data-path="api/admin/settings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-settings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-settings"
                    onclick="tryItOut('GETapi-admin-settings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-settings"
                    onclick="cancelTryOut('GETapi-admin-settings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-settings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/settings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="pengaturan-admin-POSTapi-admin-settings">Update Pengaturan Admin</h2>

<p>
</p>

<p>Memperbarui pengaturan break mode dan jam istirahat.</p>

<span id="example-requests-POSTapi-admin-settings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/admin/settings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"break_mode_active\": \"false\",
    \"break_start_time\": \"12:00\",
    \"break_end_time\": \"14:00\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/settings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "break_mode_active": "false",
    "break_start_time": "12:00",
    "break_end_time": "14:00"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/admin/settings'
payload = {
    "break_mode_active": "false",
    "break_start_time": "12:00",
    "break_end_time": "14:00"
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-settings">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;break_mode_active&quot;: &quot;false&quot;,
        &quot;break_start_time&quot;: &quot;12:00&quot;,
        &quot;break_end_time&quot;: &quot;14:00&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-admin-settings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-settings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-settings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-settings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-settings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-settings" data-method="POST"
      data-path="api/admin/settings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-settings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-settings"
                    onclick="tryItOut('POSTapi-admin-settings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-settings"
                    onclick="cancelTryOut('POSTapi-admin-settings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-settings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/settings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>break_mode_active</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="break_mode_active"                data-endpoint="POSTapi-admin-settings"
               value="false"
               data-component="body">
    <br>
<p>Aktifkan break mode manual (true/false). Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>break_start_time</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="break_start_time"                data-endpoint="POSTapi-admin-settings"
               value="12:00"
               data-component="body">
    <br>
<p>Jam mulai istirahat. Example: <code>12:00</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>break_end_time</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="break_end_time"                data-endpoint="POSTapi-admin-settings"
               value="14:00"
               data-component="body">
    <br>
<p>Jam selesai istirahat. Example: <code>14:00</code></p>
        </div>
        </form>

                <h1 id="telemetri-ai">Telemetri AI</h1>

    

                                <h2 id="telemetri-ai-POSTapi-telemetry-log">Kirim Log Telemetri Deteksi</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Endpoint untuk menerima data hasil deteksi anomali dari edge device (Python pipeline).
Setiap log yang diterima akan disimpan ke database, label akan di-normalisasi via firstOrCreate,
dan event broadcast akan dipicu untuk realtime update di dashboard.</p>

<span id="example-requests-POSTapi-telemetry-log">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/telemetry/log" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"camera_id\": \"CAM-001\",
    \"label_detected\": \"[Wanita] R-PKN-001: Tidak mengenakan hijab\",
    \"confidence_score\": 0.92
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/telemetry/log"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "camera_id": "CAM-001",
    "label_detected": "[Wanita] R-PKN-001: Tidak mengenakan hijab",
    "confidence_score": 0.92
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/telemetry/log'
payload = {
    "camera_id": "CAM-001",
    "label_detected": "[Wanita] R-PKN-001: Tidak mengenakan hijab",
    "confidence_score": 0.92
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-telemetry-log">
            <blockquote>
            <p>Example response (201, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;camera_id&quot;: &quot;CAM-001&quot;,
        &quot;label_id&quot;: 1,
        &quot;confidence_score&quot;: &quot;0.920&quot;,
        &quot;created_at&quot;: &quot;2026-06-19T10:00:00.000000Z&quot;,
        &quot;label_detected&quot;: &quot;[Wanita] R-PKN-001: Tidak mengenakan hijab&quot;,
        &quot;camera&quot;: {
            &quot;id&quot;: &quot;CAM-001&quot;,
            &quot;location_name&quot;: &quot;Taman Riyadhah&quot;,
            &quot;latitude&quot;: &quot;5.18020000&quot;,
            &quot;longitude&quot;: &quot;97.15070000&quot;,
            &quot;is_active&quot;: true
        },
        &quot;label&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;[Wanita] R-PKN-001: Tidak mengenakan hijab&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthorized):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unauthorized&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validasi Gagal):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The camera id field is required.&quot;,
    &quot;errors&quot;: {
        &quot;camera_id&quot;: [
            &quot;The camera id field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-telemetry-log" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-telemetry-log"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-telemetry-log"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-telemetry-log" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-telemetry-log">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-telemetry-log" data-method="POST"
      data-path="api/telemetry/log"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-telemetry-log', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-telemetry-log"
                    onclick="tryItOut('POSTapi-telemetry-log');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-telemetry-log"
                    onclick="cancelTryOut('POSTapi-telemetry-log');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-telemetry-log"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/telemetry/log</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-telemetry-log"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-telemetry-log"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-telemetry-log"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>camera_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="camera_id"                data-endpoint="POSTapi-telemetry-log"
               value="CAM-001"
               data-component="body">
    <br>
<p>ID kamera terdaftar. Example: <code>CAM-001</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label_detected</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="label_detected"                data-endpoint="POSTapi-telemetry-log"
               value="[Wanita] R-PKN-001: Tidak mengenakan hijab"
               data-component="body">
    <br>
<p>Label hasil deteksi AI. Example: <code>[Wanita] R-PKN-001: Tidak mengenakan hijab</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>confidence_score</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="confidence_score"                data-endpoint="POSTapi-telemetry-log"
               value="0.92"
               data-component="body">
    <br>
<p>Skor kepercayaan deteksi (0.0 - 1.0). Example: <code>0.92</code></p>
        </div>
        </form>

                    <h2 id="telemetri-ai-GETapi-telemetry-latest">Ambil Log Deteksi Terbaru (Polling)</h2>

<p>
</p>

<p>Endpoint polling ringan untuk mendapatkan log deteksi terbaru.
Gunakan parameter <code>after_id</code> untuk incremental polling (hanya ambil data baru setelah ID tertentu).
Gunakan parameter <code>camera_id</code> untuk filter per kamera.</p>

<span id="example-requests-GETapi-telemetry-latest">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/telemetry/latest?after_id=150&amp;camera_id=CAM-001&amp;limit=30" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"after_id\": 27,
    \"camera_id\": \"architecto\",
    \"limit\": 22
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/telemetry/latest"
);

const params = {
    "after_id": "150",
    "camera_id": "CAM-001",
    "limit": "30",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "after_id": 27,
    "camera_id": "architecto",
    "limit": 22
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost:8000/api/telemetry/latest'
payload = {
    "after_id": 27,
    "camera_id": "architecto",
    "limit": 22
}
params = {
  'after_id': '150',
  'camera_id': 'CAM-001',
  'limit': '30',
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, json=payload, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-telemetry-latest">
            <blockquote>
            <p>Example response (200, Sukses):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 151,
            &quot;camera_id&quot;: &quot;CAM-001&quot;,
            &quot;label_id&quot;: 1,
            &quot;confidence_score&quot;: &quot;0.920&quot;,
            &quot;created_at&quot;: &quot;2026-06-19T10:00:00.000000Z&quot;,
            &quot;label_detected&quot;: &quot;[Wanita] R-PKN-001&quot;,
            &quot;camera&quot;: {
                &quot;id&quot;: &quot;CAM-001&quot;,
                &quot;location_name&quot;: &quot;Taman Riyadhah&quot;
            },
            &quot;label&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;[Wanita] R-PKN-001&quot;
            }
        }
    ],
    &quot;meta&quot;: {
        &quot;total_today&quot;: 42,
        &quot;latest_id&quot;: 151
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-telemetry-latest" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-telemetry-latest"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-telemetry-latest"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-telemetry-latest" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-telemetry-latest">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-telemetry-latest" data-method="GET"
      data-path="api/telemetry/latest"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-telemetry-latest', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-telemetry-latest"
                    onclick="tryItOut('GETapi-telemetry-latest');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-telemetry-latest"
                    onclick="cancelTryOut('GETapi-telemetry-latest');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-telemetry-latest"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/telemetry/latest</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-telemetry-latest"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-telemetry-latest"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="after_id"                data-endpoint="GETapi-telemetry-latest"
               value="150"
               data-component="query">
    <br>
<p>ID log terakhir yang sudah diterima client. Hanya log dengan ID lebih besar akan dikembalikan. Example: <code>150</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>camera_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="camera_id"                data-endpoint="GETapi-telemetry-latest"
               value="CAM-001"
               data-component="query">
    <br>
<p>Filter berdasarkan ID kamera. Example: <code>CAM-001</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-telemetry-latest"
               value="30"
               data-component="query">
    <br>
<p>Jumlah maksimal log yang dikembalikan (default: 30, max: 100). Example: <code>30</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>after_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="after_id"                data-endpoint="GETapi-telemetry-latest"
               value="27"
               data-component="body">
    <br>
<p>validation.min. Example: <code>27</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>camera_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="camera_id"                data-endpoint="GETapi-telemetry-latest"
               value="architecto"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-telemetry-latest"
               value="22"
               data-component="body">
    <br>
<p>validation.min validation.max. Example: <code>22</code></p>
        </div>
        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="python">python</button>
                            </div>
            </div>
</div>
</body>
</html>
