function mulberry32(a) {
    return function() {
        let t=a+=0x6D2B79F5
        t=Math.imul(t^t>>>15,t|1)
        t^=t+Math.imul(t^t>>>7,t|61)
        return ((t^t>>>14)>>>0)/4294967296
    }
}
//game timer
const startDate=new Date("2127-01-01").getTime(), realStartDate=new Date("2023-12-23").getTime()
const speedup=20

function gameTimer(){
    return new Date(startDate+(Date.now()-realStartDate)*speedup)/(1000*60*60*24)
}

function gameTimerAsDate(t){
    if(typeof t === "undefined") t=gameTimer()
    return new Date(t*(1000*60*60*24))
}

function numberTo2Digits(n){
    if(n>=0&&n<10) return "0"+n; else return ""+n;
}

function generateValue(variability, volatility, noisiness, valoreBase, seed) {
    timestamp = gameTimer();
    timestamp = Number(timestamp).toFixed(5);
    
    let v = 0;
    let sumAmplitudes = 0;

    let hAmplitude = [], hPhase = [], hfhAmplitude = [], hfhPhase = [];

    let rng = mulberry32(seed)

    for(let i=0;i<1024;i++){
        hAmplitude[i]=variability*(rng()*1000/(i*0.7+rng()*0.3))
        hPhase[i]=rng()*2*Math.PI
    }
    for(let i=0;i<1024;i++){
        hfhAmplitude[i]=noisiness*(rng()/(i*0.3+rng()*0.7))
        hfhPhase[i]=rng()*2*Math.PI
    }

    let baseF=volatility/100

    // Calcolo armoniche basse
    for (let i = 0; i < hAmplitude.length; i++) {
        v += hAmplitude[i] * Math.sin(baseF * timestamp * i + hPhase[i]);
        sumAmplitudes += hAmplitude[i];
    }

    // Calcolo armoniche alte
    for (let i = 0; i < hfhAmplitude.length; i++) {
        let hfValue = hfhAmplitude[i] * Math.sin(baseF * 1000 * timestamp * i + hfhPhase[i]);
        let ultraHfValue = hfhAmplitude[i] * Math.sin(baseF * 100000 * timestamp * i + hfhPhase[i]) * 0.2;
        v += hfValue + ultraHfValue;
        sumAmplitudes += hfhAmplitude[i];
    }

    v /= sumAmplitudes; // Rende maggiormente stabile il valore di v
    v += 1;

    v = Math.abs(v) * valoreBase;

    // Ottimizzazione della trasformazione per valori bassi
    v = v < 0.1 ? Math.exp(v - 2.4) : v;
    v = Math.max(v - 0.05, 0.001);
    
    return v;
}

function previous(variability, volatility, noisiness, valoreBase, seed, t) {
    t = Number(t).toFixed(5);
    
    let v = 0;
    let sumAmplitudes = 0;

    let hAmplitude = [], hPhase = [], hfhAmplitude = [], hfhPhase = [];

    let rng = mulberry32(seed)

    for(let i=0;i<1024;i++){
        hAmplitude[i]=variability*(rng()*1000/(i*0.7+rng()*0.3))
        hPhase[i]=rng()*2*Math.PI
    }
    for(let i=0;i<1024;i++){
        hfhAmplitude[i]=noisiness*(rng()/(i*0.3+rng()*0.7))
        hfhPhase[i]=rng()*2*Math.PI
    }

    let baseF=volatility/100

    // Calcolo armoniche basse
    for (let i = 0; i < hAmplitude.length; i++) {
        v += hAmplitude[i] * Math.sin(baseF * t * i + hPhase[i]);
        sumAmplitudes += hAmplitude[i];
    }

    // Calcolo armoniche alte
    for (let i = 0; i < hfhAmplitude.length; i++) {
        let hfValue = hfhAmplitude[i] * Math.sin(baseF * 1000 * t * i + hfhPhase[i]);
        let ultraHfValue = hfhAmplitude[i] * Math.sin(baseF * 100000 * t * i + hfhPhase[i]) * 0.2;
        v += hfValue + ultraHfValue;
        sumAmplitudes += hfhAmplitude[i];
    }

    v /= sumAmplitudes; // Rende maggiormente stabile il valore di v
    v += 1;

    v = Math.abs(v) * valoreBase;

    // Ottimizzazione della trasformazione per valori bassi
    v = v < 0.1 ? Math.exp(v - 2.4) : v;
    v = Math.max(v - 0.05, 0.001);
    
    return v;
}