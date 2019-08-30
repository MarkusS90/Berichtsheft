using System;
using System.Runtime.Serialization;

namespace ReportWcfService
{
    [DataContract]
    public class Activity
    {
        [DataMember]
        public String Caption { get; set; }

        [DataMember]
        public Int32 Duration { get; set; }

        public Activity() { }
    }
}