

console.log('🔍 MOBILE_VIEW: JS script executed from file!');

function mobileStartCheck() {
    document.getElementsByClassName("mobile-boxes-container")[0].classList.add('.hidden');
    document.getElementById("mobile-check-area").classList.remove('.hidden');
    console.log('🔍 MOBILE_VIEW: Showing Check now.!');
}


window.mobileStartCheck = mobileStartCheck;
console.log('🔍 MOBILE_VIEW: window.mobileStartCheck =', typeof window.mobileStartCheck);
