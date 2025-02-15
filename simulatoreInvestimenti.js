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
//let _debug_tOffset=0
function gameTimer(){
    return new Date(startDate+(Date.now()-realStartDate)*speedup/*+_debug_tOffset*/)/(1000*60*60*24)
}
function gameTimerAsDate(t){
    if(typeof t === "undefined") t=gameTimer()
    return new Date(t*(1000*60*60*24))
}
//stock simulation
function Stock(id,name,description,baseValue,variability,volatility,noisiness,influenceability,commission,tax,dividend,seed){
    this.id=id
    this.name=name
    this.description=description
    this.type="stock"
    this._rng=mulberry32(seed)
    this._baseValue=baseValue
    this._variability=variability
    this._volatility=volatility
    this._noisiness=noisiness
    this._influenceability=influenceability
    this._baseF=volatility/100
    this._hAmplitude=[]
    this._hPhase=[]
    this._hfhAmplitude=[]
    this._hfhPhase=[]
    this._influenceability=influenceability
    this.commission=commission
    this.tax=tax
    this.dividend=dividend
    for(let i=0;i<1024;i++){
        this._hAmplitude[i]=variability*(this._rng()*1000/(i*0.7+this._rng()*0.3))
        this._hPhase[i]=this._rng()*2*Math.PI
    }
    for(let i=0;i<1024;i++){
        this._hfhAmplitude[i]=noisiness*(this._rng()/(i*0.3+this._rng()*0.7))
        this._hfhPhase[i]=this._rng()*2*Math.PI
    }
    this._valueCache=[]
    /*this._cacheHits=0
    this._cacheMisses=0*/
}
const STOCKVALUECACHE_SIZE=1048576
const CACHE_TOLERANCE = 0.00001;

Stock.prototype={
    constructor:Stock,
    getValue: function (t = gameTimer()) {
        t = Number(t).toFixed(5);
        let tIdx = (~~(t * 100000)) % CACHE_SIZE;
    
        // Controllo cache
        if (this._valueCache[tIdx]?.t && Math.abs(this._valueCache[tIdx].t - t) < CACHE_TOLERANCE) {
            return this._valueCache[tIdx].v;
        }
        
        let v = 0;
        let sumAmplitudes = 0;
    
        // Calcolo armoniche basse
        for (let i = 0; i < this._hAmplitude.length; i++) {
            v += this._hAmplitude[i] * Math.sin(this._baseF * t * i + this._hPhase[i]);
            sumAmplitudes += this._hAmplitude[i];
        }
    
        // Calcolo armoniche alte
        for (let i = 0; i < this._hfhAmplitude.length; i++) {
            let hfValue = this._hfhAmplitude[i] * Math.sin(this._baseF * 1000 * t * i + this._hfhPhase[i]);
            let ultraHfValue = this._hfhAmplitude[i] * Math.sin(this._baseF * 100000 * t * i + this._hfhPhase[i]) * 0.2;
            v += hfValue + ultraHfValue;
            sumAmplitudes += this._hfhAmplitude[i];
        }
    
        v /= sumAmplitudes; // Rende maggiormente stabile il valore di v
        v += 1;
    
        // Piccola aggiunta casuale
        v += (Math.random() - 0.5) * 0.02;
    
        // Influenza del master stock (dovrà essere più di uno)
        if (this._influenceability !== 0) {
            v *= Math.pow(_masterStock.getValue(t), 0.75) * this._influenceability + (1 - this._influenceability);
        }
    
        v = Math.abs(v) * this._baseValue;
    
        // Ottimizzazione della trasformazione per valori bassi
        v = v < 0.1 ? Math.exp(v - 2.4) : v;
        v = Math.max(v - 0.05, 0.001);
    
        // Salvataggio nella cache
        this._valueCache[tIdx] = { t, v };
        
        return v;
    },
    getDailyVariation:function(){
        let t=gameTimer()
        return (this.getValue(t)/this.getValue(t-1))-1
    },
    getRelativeStrengthIndex(t){
        //New algorithm using exponential moving average instead of regular average
        if(typeof t === "undefined") t=gameTimer()
        let gains,losses,v,p,dv,dg,dl
        for(let i=0;i<14;i++){
            v=this.getValue(t-i)
            p=this.getValue(t-i-1)
            dv=v/p-1
            if(dv>=0){
                dg=dv
                dl=0
            }else{
                dg=0
                dl=-dv
            }
            if(i>0){
                gains=dg*0.15+gains*0.85
                losses=dl*0.15+losses*0.85
            }else{
                gains=dg
                losses=dl
            }
        }
        if(losses==0) return 100
        return 100-(100/(1+(gains/losses)))
    },
    getNextDividendT(){
        if(!this.dividend) return null
        //TODO: i have no idea why the old formula didn't work so i'm doing this crappy workaround by simulating the passing of days
        let t=gameTimer()
        let dilt=~~((t+this.dividend.offset)/this.dividend.frequency)
        for(let i=t;i<=t+this.dividend.frequency;i++){
            let dit=~~((i+this.dividend.offset)/this.dividend.frequency)
            if(dit>dilt) return i
        }
    },
    getLongTermInvestmentRating(){
        let score=0
        let n=Math.log(this._baseValue*0.05+1)
        score+=n>3?3:n
        n=Math.log(this._variability*1.5+1)
        score+=n>3?3:n
        n=0.2/this._volatility
        score+=n>2?2:n
        n=Math.log(Math.abs(this._influenceability)+1)
        score+=n>2?2:n
        score=Math.pow(Math.abs(score),0.9)
        score*=1-this.tax
        return score
    },
    getSpeculativeInvestmentRating(){
        let score=0
        let n=Math.log(this._variability+1)
        score+=n>2?2:n
        n=Math.log(this._volatility+1)
        score+=n>2?2:n
        n=Math.log(this._noisiness+1)
        score+=n>2?2:n
        let min=9999999,max=0
        let t=~~(gameTimer()/10)*10
        for(let i=0;i<365;i+=10){
            let v=this.getValue(t-i)
            if(v<min) min=v
            if(v>max) max=v
        }
        score+=(max-min)/min
        score=Math.pow(Math.abs(score),1.1)
        score*=1-this.tax
        return score
    }
}

const _masterStock=new Stock("","","",1,10,0.002,0,0,0,0,null,437)