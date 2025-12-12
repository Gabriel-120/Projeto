
if(document.cookie.split("auth=").includes( 'true')){
    document.querySelector("#login").classList.add("hidden");
    document.querySelector("#user").classList.remove("hidden");
}